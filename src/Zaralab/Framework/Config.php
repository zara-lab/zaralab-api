<?php
/**
 * Project: zaralab
 * Filename: Config.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 02.11.15
 */

namespace Zaralab\Framework;


use Slim\Container;

class Config
{
    const VERSION = '0.0.1';

    /**
     * @param string $appPath path to app/ folder
     * @return Container
     * @todo Application environment based settings
     */
    public static function containerFactory($appPath)
    {
        if (!is_readable($appPath.'/settings.php')) {
            throw new \RuntimeException("Application configuration file not found.");
        }

        $env = getenv('SLIM3_ENV') ?: 'dev';
        $debug = getenv('SLIM3_DEBUG') !== '0' && $env != 'prod';
        $params = [];
        if (is_readable($appPath.'/parameters.php')) {
            $params = include $appPath.'/parameters.php';
        }

        $config = require $appPath.'/settings.php';


        $config['ENV'] = $env;
        $config['DEBUG'] = $debug;
        $config['VERSION'] = static::VERSION;
        $config['BASEPATH'] = realpath($appPath.'/..');
        $config['APPNAME'] = isset($params['app.name']) ? $params['app.name'] : basename($config['BASEPATH']);

        return new Container($config);
    }
}