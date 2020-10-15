<?php

namespace Framework\CSRF;

use Framework\Request\Request;
use Framework\Exceptions\HttpExceptions;

class CSRF
{
    /**
     * Check if the cookie isset if not generate one
     * 
     * @return void
     */
    public static function init() : void
    {
        if (!isset($_COOKIE['CSRFToken'])) {
            setcookie('CSRFToken', bin2hex(random_bytes(32)), time() + (86400 * 30), '/', getenv('DOMAIN'), getenv('SECURE'), getenv('HTTP_ONLY'));
        }
    }

    /**
     * Check if CSRF token is correct and if it is sent with the form
     * 
     * @param Request $request
     * @return void
     */
    public static function checkCSRFtoken(Request $request) : void
    {
        $requestMethod = $request->requestMethod();

        if ($request->input('CSRFtoken') !== null) {
            if ($request->input('CSRFtoken') !== $_COOKIE['CSRFToken']) {
                throw new HttpExceptions('CSRF token validation failed. Please try again.', 403);
            }
        }
        else if ($requestMethod === 'POST' || $requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            throw new HttpExceptions('There was no CSRF token found on the form submission. Please disable CSRF for this route or add a CSRF token to the request body.', 403);
        }
    }
}


