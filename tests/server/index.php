<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__.$_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array(
        '127.0.0.1',
        '::1',
    ))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file.');
}

require __DIR__.'/../../vendor/autoload.php';

session_start();

$_ENV['SLIM3_ENV'] = 'test';
$_ENV['SLIM3_DEBUG'] = true;

// Container
$container = \Zaralab\Framework\Config::containerFactory(__DIR__.'/../../app', 'test', true);

// Instantiate the app
$app = new \Slim\App($container);

// Set up dependencies
require __DIR__.'/../../app/dependencies.php';

// Register middleware
require __DIR__.'/../../app/middleware.php';

// Register routes
require __DIR__.'/../../app/routes.php';


// Run!
$app->run();
