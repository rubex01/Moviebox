<?php

namespace Framework\Routing;

use App\Config;
use Framework\Request\Request;
use ReflectionClass;
use Framework\CSRF\CSRF;
use Framework\Exceptions\HttpExceptions;
use Framework\Auth\Auth;

class Router
{
    /**
     * Contains all routes
     * 
     * @var array
     */
    protected static $routes = [];

    /**
     * Contains current route
     * 
     * @var array
     */
    protected static $currentRoute;

    /**
     * Contains path that isn't found
     * 
     * @var string
     */
    protected static $pathNotFound = null;

    /**
     * Contains method that isn't allowed
     * 
     * @var string
     */
    private static $methodNotAllowed = null;

    /**
     * Contains path that is found
     * 
     * @var string
     */
    private static $pathMatchFound = false;

    /**
     * Contains route that is found
     * 
     * @var string
     */
    private static $routeMatchFound = false;

    /**
     * Contains request object
     * 
     * @var object
     */
    private static $request;

    /**
     * Contains basepath
     * 
     * @var string
     */
    private static $basepath = BASEPATH;

    /**
     * Add route to routes array
     * 
     * @param string $expression
     * @param $function
     * @param string $method
     * @return void
     */
    public static function addRoute(string $expression, $function, string $method = 'get') : void
    {
        if (is_array($function)) {
            $function = [
                'class' => $function[0],
                'method' => $function[1]
            ];
        }

        self::$routes[] = [
            'expression' => $expression,
            'function' => $function,
            'method' => $method
        ];

        self::$currentRoute = [
            'id' => array_key_last(self::$routes),
            'expression' => $expression,
            'function' => $function,
            'method' => $method
        ];
    }

    /**
     * Run routes functions
     * 
     * @return void
     */
    public static function run() : void
    {
        self::$request = new Request();

        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

        if (isset($parsedUrl['path'])) {
            $path = $parsedUrl['path'];
        } else {
            $path = '/';
        }

        self::runFunctionForRoute($path);
    }

    /**
     * Disable the CSRF check for the current route
     * 
     * @return object
     */
    public static function disableCSRF() : object
    {
        self::$routes[self::$currentRoute['id']]['csrf'] = false;
        return new static;
    }

    /**
     * Run CSRF check on current route with request object
     * 
     * @param array $route
     * @return void
     */
    private static function runCSRFcheck(array $route) : void
    {
        if (!isset($route['csrf'])) {
            try {
                CSRF::checkCSRFtoken(self::$request);
            } 
            catch (HttpExceptions $th) {
                exit();
            }
        }
    }

    /**
     * Find requested route and run the specified function or run method function
     * 
     * @param string $path
     * @return void
     */
    private static function runFunctionForRoute (string $path) : void
    {
        $basepath = self::$basepath;

        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {

            if ($basepath != '' && $basepath != '/') {
                $route['expression'] = '(' . $basepath . ')' . $route['expression'];
            }

            $route['expression'] = '^' . $route['expression'];
            $route['expression'] = $route['expression'] . '$';

            if (preg_match('#' . $route['expression'] . '#', $path, $matches)) {

                self::$pathMatchFound = true;

                if (strtolower($method) == strtolower($route['method'])) {
                    array_shift($matches);

                    self::runCSRFcheck($route);
 
                    if ($basepath != '' && $basepath != '/') {
                        array_shift($matches);
                    }

                    if (isset($route['action'])) {
                        self::runActionRoleCheck($route['action']);
                    }

                    if (isset($route['middleware'])) {
                        self::runRouteMiddleware($route['middleware']);
                    }

                    if (count($matches) > 0) {
                        $matches = self::urldecodeMatchesArray($matches);
                    }

                    if (is_array($route['function'])) {
                        self::runMethodRoute(
                            $route['function']['class'],
                            $route['function']['method'],
                            $matches
                        );
                    } else {
                        call_user_func_array($route['function'], $matches);
                    }

                    self::$routeMatchFound = true;

                    break;
                }
            }
        }

        if (!self::$routeMatchFound) {
            if (self::$pathMatchFound) {
                self::methodNotAllowed($path, $method);
            }
            else {
                self::pathNotFound($path, $method);
            }
        }
    }

    /**
     * Method is not allowed
     * 
     * @param string $path
     * @param string $method
     * @return void
     */
    public static function methodNotAllowed($path, $method) : void
    {
        //todo:: give correct error w error handler
        echo 'Error 405 :-(<br>';
        echo 'The requested path "' . $path . '" exists. But the request method "' . $method . '" is not allowed on this path!';
        
        header('HTTP/1.0 405 Method Not Allowed');
    }

    /**
     * Check if user has right role to complete action
     *
     * @param string $action
     * @return bool
     */
    public static function runActionRoleCheck(string $action) : bool
    {
        try {
            if (!file_exists(__DIR__ . '/../../Roles.json')) {
                throw new HttpExceptions('Please enable the roles functionality by typing "php .\Framework\Commands\Roles.php" in the command line.', 500);
            }
            $rolesAndActions = json_decode(file_get_contents(__DIR__ . '/../../Roles.json'));
            $roleKey = array_search(Auth::$user['role'], $rolesAndActions->roles);
            if (isset($rolesAndActions->actions->$action) && in_array($roleKey, $rolesAndActions->actions->$action)) {
                return true;
            }
            throw new HttpExceptions('You do not have the permission to execute this action', 401);
        }
        catch (HttpExceptions $th) {
            exit();
        }
    }

    /**
     * Path is not found
     * 
     * @param string $path
     * @return void
     */
    public static function pathNotFound($path) : void
    {
        //todo:: give correct error w error handler
        echo 'Error 404 :-(<br>';
        echo 'The requested path "' . $path . '" was not found!';
        header('HTTP/1.0 404 Not Found');
    }

    /**
     * Run method for route
     * 
     * @param string $controller
     * @param string $methodName
     * @param array $matches
     * @return void
     */
    private static function runMethodRoute(string $controller, string $methodName, array $matches) : void
    {
        $reflectionClass = new ReflectionClass($controller);
        
        $parameters = $reflectionClass->getMethod($methodName)->getParameters();

        if (count($parameters) > 0) {
            $firstParam = $parameters[0]->getClass()->name;
        }
        else {
            $firstParam = null;
        }
        
        if ($firstParam === 'Framework\Request\Request') {
            array_unshift($matches, self::$request);
        }

        $routeClass = new $controller;
        $routeClass->$methodName(...$matches);
    }

    /**
     * Url decode variables found in url
     * 
     * @param array $matches
     * @return array
     */
    private function urldecodeMatchesArray(array $matches) : array
    {
        $newMatches = [];
        foreach ($matches as $match) {
            $newMatches[] = urldecode($match);
        }
        return $newMatches;
    }

    /**
     * Run middleware for route
     * 
     * @param array $middlewareArray
     * @return void
     */
    private function runRouteMiddleware(array $middlewareArray) : void
    {
        $allMiddleware = Config::$routeMiddleware;

        foreach ($middlewareArray as $middlewareString) {
            if (isset($allMiddleware[$middlewareString])) {
                $runMiddleware = new $allMiddleware[$middlewareString];
                if ($runMiddleware->handle() === false) {
                    $runMiddleware->onFailure();
                    exit();
                }
            }
            else {
                try {
                    throw new HttpExceptions('Middleware function has not been declared in the config file.', 500);
                } catch (HttpExceptions $th) {
                    exit();
                }
            }
        }
    }

}