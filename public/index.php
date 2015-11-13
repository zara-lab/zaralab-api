<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__.$_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__.'/../vendor/autoload.php';

session_start();

// Container
$container = \Zaralab\Framework\Config::containerFactory(__DIR__.'/../app', null, false);

// Instantiate the app
$app = new \Slim\App($container);

// Set up dependencies
require __DIR__.'/../app/dependencies.php';

// Register middleware
require __DIR__.'/../app/middleware.php';

// Register routes
require __DIR__.'/../app/routes.php';

// Run!
$app->run();
