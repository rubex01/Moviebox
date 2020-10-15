<?php

use Framework\Routing\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here you can register new routes for your app. See the docs to get
| started. Now create something nice!
|
*/

// Get transmission session id
$transmissionId = App\Controllers\TransmissionSessionController::getSessionId();
define('TRANSMISSIONID', $transmissionId);

session_start();

Route::get('/', [App\Controllers\PagesController::class, 'getIndex']);

Route::get('/login/(.*)/(.*)', [App\Controllers\PagesController::class, 'getLogin']);

Route::get('/register', [App\Controllers\PagesController::class, 'getRegister']);

Route::group([
    'prefix' => '/endpoint',
    'routes' => [
        Route::post('/login', [App\Controllers\AuthController::class, 'login']),
        Route::post('/register', [App\Controllers\AuthController::class, 'register']),
        Route::get('/logout', [App\Controllers\AuthController::class, 'logout'])
    ]
]);

Route::group([
    'middleware' => ['auth'],
    'routes' => [
        Route::get('/home', [App\Controllers\PagesController::class, 'getHome']),
        Route::get('/search', [App\Controllers\Movies\SearchController::class, 'search']),
        Route::get('/watch/(.*)/(.*)/(.*)/(.*)', [App\Controllers\Movies\WatchController::class, 'watchStart']),
        Route::get('/downloads', [App\Controllers\Movies\WatchController::class, 'getDownloads']),
        Route::get('/download-status', [App\Controllers\Movies\ApiController::class, 'getDownloadStatus']),
        Route::get('/download-status/(.*)', [App\Controllers\Movies\ApiController::class, 'getDownloadStatusSingle']),
        Route::post('/time/(.*)', [App\Controllers\Movies\ApiController::class, 'setTime']),
        Route::get('/popcorn/(.*)', [App\Controllers\Movies\WatchController::class, 'getMovie']),
        Route::get('/delete/(.*)', [App\Controllers\Movies\WatchController::class, 'deleteMovie'])
    ]
]);