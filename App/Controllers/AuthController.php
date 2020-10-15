<?php

namespace App\Controllers;

use Framework\Auth\Auth;
use Framework\Database\Database;
use Framework\Request\Request;
use Framework\Validation\Validation;

class AuthController
{
    public function login(Request $request)
    {
        $validation = new Validation($request->getBodyData(),
            [
                'email' => ['required', 'email', 'exists:users'],
                'password' => ['required', 'string']
            ]
        );

        if (count($validation->validationErrors) > 0) {
            $_SESSION['errors'] = $validation->validationErrors;
            header('location:'.$request->server['HTTP_REFERER']);
            exit();
        }

        $login = Auth::login(['email' => $request->input('email'), 'password' => $request->input('password')]);
        if ($login === true) {
            header('location:/home');
            exit();
        }

        $_SESSION['errors'] = [['message' => 'Credentials are incorrect']];
        header('location:'.$request->server['HTTP_REFERER']);
    }

    public function register(Request $request)
    {
        $validation = new Validation($request->getBodyData(),
            [
                'username' => ['required', 'unique:users'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'string', 'confirmation']
            ]
        );

        if (count($validation->validationErrors) > 0) {
            $_SESSION['errors'] = $validation->validationErrors;
            header('location:/register');
            exit();
        }

        $username = $request->input('username');
        $email = $request->input('email');
        $hashedPassword = password_hash($request->input('password'), PASSWORD_BCRYPT);
        $color = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

        $stmt = Database::$Connections['MySQL']->prepare("INSERT INTO users (username, email, password, color) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss',
            $username,
            $email,
            $hashedPassword,
            $color
        );
        $stmt->execute();

        header('location:/login/'.$request->input('email').'/'.$color);
    }

    public function logout()
    {
        Auth::logout();
        header('location:/');
    }
}