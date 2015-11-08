<?php
/**
 * Project: zaralab
 * Filename: console.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 31.10.15
 */

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Input\ArgvInput;
use Zaralab\Framework\Console\App;
use Zaralab\Framework\Config;

// Override DEBUG/ENV
$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), null);
$debug = $env === null ? null : !$input->hasParameterOption(array('--no-debug', '')) && $env != 'prod';

$container = Config::containerFactory(__DIR__, $env, $debug);

// Set current directory to application root so we can find root config files
chdir(__DIR__ . '/..');

$app = new App($container);
$app->setCatchExceptions(true);

// Set up DIC
require __DIR__.'/dependencies.php';

$app->run($input);
