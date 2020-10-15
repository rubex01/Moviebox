<?php

namespace {

    include __DIR__ . '/../Framework/autoload.php';
    include __DIR__ . '/../Framework/bootstrap.php';

    use Framework\Auth\Auth;
    use Framework\Routing\Route;
    use App\Config;

    include __DIR__ . '/../Web/DBConnections.php';

    if (Config::$enabledFunctions['authorization']) {
        $Authorization = new Auth();
    }

    include __DIR__ . '/../Web/Routes.php';

    Route::run();

}

