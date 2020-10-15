<?php

namespace App\Controllers;

use Framework\Database\Database;
use Framework\Pages\Pages;
use Framework\Request\Request;
use Framework\Auth\Auth;

class PagesController
{
    public function __construct()
    {
        $this->database = Database::$Connections['MySQL'];
    }

    public function getIndex()
    {
        $stmt = $this->database->prepare("SELECT username, email, color FROM users");
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_NUM);

        return Pages::view('authentication', 'home', [
            'CSS' => ['auth/home.css'],
            'titleAppend' => 'Home',
            'users' => $users
        ]);
    }

    public function getRegister()
    {
        return Pages::view('authentication', 'register', [
            'CSS' => ['auth/register.css'],
            'titleAppend' => 'Register',
        ], true);
    }

    public function getLogin(Request $request, string $email, string $color)
    {
        Pages::view('authentication', 'login', [
            'CSS' => ['auth/login.css'],
            'titleAppend' => 'Login',
            'color' => $color,
            'email' => $email
        ], true);
    }

    public function getHome()
    {
        return Pages::view('default', 'movies/home', [
            'CSS' => ['movies/home.css'],
            'titleAppend' => 'Home',
        ], false);
    }
}