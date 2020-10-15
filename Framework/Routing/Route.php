<?php

namespace Framework\Routing;

use Framework\Routing\Router;

class Route extends Router
{
    /**
     * Add get method route
     * 
     * @param string $path
     * @param $function
     * @return object
     */
    public static function get($path, $function) : object
    {
        self::addRoute($path, $function, 'get');
        return new static;
    }

    /**
     * Add post method route
     * 
     * @param string $path
     * @param $function
     * @return object
     */
    public static function post($path, $function) : object
    {
        self::addRoute($path, $function, 'post');
        return new static;
    }

    /**
     * Add put method route
     * 
     * @param string $path
     * @param $function
     * @return object
     */
    public static function put($path, $function) : object
    {
        self::addRoute($path, $function, 'put');
        return new static;
    }

    /**
     * Add patch method route
     * 
     * @param string $path
     * @param $function
     * @return object
     */
    public static function patch($path, $function) : object
    {
        self::addRoute($path, $function, 'patch');
        return new static;
    }

    /**
     * Add delete method route
     * 
     * @param string $path
     * @param $function
     * @return object
     */
    public static function delete($path, $function) : object
    {
        self::addRoute($path, $function, 'delete');
        return new static;
    }

    /**
     * Add options method route
     * 
     * @param string $path
     * @param $function
     * @return object
     */
    public static function options($path, $function) : object
    {
        self::addRoute($path, $function, 'options');
        return new static;
    }

    /**
     * Add middleware to route
     * 
     * @param array $middlewareArray
     * @return object
     */
    public static function middleware(array $middlewareArray) : object
    {
        foreach ($middlewareArray as $middleware) {
            self::$routes[self::$currentRoute['id']]['middleware'][] = $middleware;
        }
        return new static;
    }

    /**
     * Add action to route to later check if permission needs to be granted
     *
     * @param string $action
     * @return object
     */
    public static function action(string $action) : object
    {
        self::$routes[self::$currentRoute['id']]['action'] = $action;
        return new static;
    }

    /**
     * Group of routes that have a prefix or have the same middleware applied to them
     *
     * @param array $groupInformation
     * @return object
     */
    public static function group(array $groupInformation) : object
    {
        foreach ($groupInformation['routes'] as $key => $route) {

            $key = $route::$currentRoute['id']-$key;

            $groupRoute = self::$routes[$key];

            if (isset($groupInformation['prefix'])) {
                $groupRoute['expression'] = $groupInformation['prefix'] . $groupRoute['expression'];
            }

            if (isset($groupInformation['middleware'])) {
                foreach ($groupInformation['middleware'] as $middleware) {
                    $groupRoute['middleware'][] = $middleware;
                }
            }

            self::$routes[$key] = $groupRoute;
        }
        return new static;
    }
}