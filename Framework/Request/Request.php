<?php

namespace Framework\Request;

class Request
{
    /**
     * Contains headers of request
     * 
     * @var array
     */
    public $headers;
    
    /**
     * Contains body of request
     * 
     * @var array
     */
    public $body;

    /**
     * Contains server information of request
     * 
     * @var array
     */
    public $server;

    /**
     * Calls all functions to set information
     * 
     * @return void
     */
    public function __construct()
    {
        $this->headers = $this->getAllHeaders();
        $this->body = $this->getBodyData();
        $this->server = $this->getServerData();
    }
    
    /**
     * Get all headers
     * 
     * @return array
     */
    public function getAllHeaders() : array
    {
        return getallheaders();
    }
    
    /**
     * Gets body data from request
     * 
     * @return array
     */
    public function getBodyData() : array
    {
        if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
            return json_decode(file_get_contents('php://input'), true);
        }
        return array_merge($_FILES, $_POST, $_GET);
    }

    /**
     * Get requested url
     * 
     * @return string
     */
    public function url() : string
    {
        return $this->server['REQUEST_URI'];
    }

    /**
     * Get the request method
     * 
     * @return string
     */
    public function requestMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * Get specified input from body
     * 
     * @parameter string $name
     * @return string|array
     */
    public function input(string $name = null) 
    {
        if ($name) {
            if (isset($this->body[$name])) {
                return $this->body[$name];
            }
            return null;
        }
        return $this->body;
    }

    /**
     * Gets server data from request
     * 
     * @return array
     */
    public function getServerData() : array
    {
        return $_SERVER;
    }   

    /**
     * Return all object values from request
     * 
     * @return array
     */
    public function all() : array
    {
        return [
            'headers' => $this->headers,
            'body' => $this->body,
            'server' => $this->server
        ];
    }
}