<?php

use Framework\TemplateEngine\TemplateEngine;

if (getenv('ENVIRONMENT') == 'production' && getenv('DEBUGGING') == false || getenv('DEBUGGING') == false) {
//    error_reporting(0);
}

if (file_exists(__DIR__ . '/../.env.php')) {
    include __DIR__ . '/../.env.php';
    foreach ($variables as $key => $value) {
        putenv("$key=$value");
    }
}
else {
    putenv("APP_NAME=DISABLED");
}

define('BASEPATH', getenv('BASEPATH'));

if (getenv('AUTOCOMPILE') == true && getenv('ENVIRONMENT') == 'development') {
    $compile = new TemplateEngine();
}