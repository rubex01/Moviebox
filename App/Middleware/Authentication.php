<?php

namespace App\Middleware;

use Framework\Auth\Auth;

class Authentication
{
    public function handle() : bool
    {
        return Auth::check();
    }

    public function onFailure()
    {
        echo 'You are not authenticated';
    }
}