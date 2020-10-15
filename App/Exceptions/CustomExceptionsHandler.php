<?php

namespace App\Exceptions;

use Exception;

class CustomExceptionsHandler extends Exception
{
    public function __construct($message, $code = 0)
    {
        // some code

        // make sure everything is assigned properly

        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}