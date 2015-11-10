<?php
/**
 * Available variables:
 * $rootPath - path to project_root
 * $appPath - path to project_root/app
 * $configPath - path to project_root/app/config
 * $params - application parameters array
 * $env - current environment e.g. dev|test|prod
 * $debug - boolean debug mode
 */
return [
    'settings' => [

        // View settings
        'view' => [
            'template_path'     => $appPath.'/templates',
            'twig' => [
                'cache'         => $rootPath.'/cache/twig',
                'debug'         => $debug,
                'auto_reload'   => $debug,
            ],
        ],

        // Monolog settings
        'logger' => [
            'name'              => varset($params['app.name'], 'app'),
            'path'              => $rootPath.'/log/app_'.$env.'.log',
        ],

        // JMS Serializer settings
        'serializer' => [
            'cache'              => $rootPath.'/cache/serializer',
        ],

        // Doctrine2 settings
        'doctrine' => [
            'dbal' => [
                'driver'    => $params['db.driver'],
                'host'      => $params['db.host'],
                'dbname'    => $params['db.name'],
                'user'      => $params['db.user'],
                'password'  => $params['db.password']
            ],
            'entities'      => [$rootPath.'/src/Zaralab/Entity'],
            'proxy_path'    => [$rootPath.'/cache/doctrine'],
        ]
    ],
];
