<?php
return [
    'settings' => [

        // View settings
        'view' => [
            'template_path'     => __DIR__.'/templates',
            'twig' => [
                'cache'         => __DIR__.'/../cache/twig',
                'debug'         => $debug,
                'auto_reload'   => $debug,
            ],
        ],

        // Monolog settings
        'logger' => [
            'name'              => 'ZaraLab',
            'path'              => __DIR__.'/../log/app_'.$env.'.log',
        ],

        // JMS Serializer settings
        'serializer' => [
            'cache'              => __DIR__.'/../cache/serializer',
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
            'entities'      => [__DIR__.'/../src/Zaralab/Entity'],
            'proxy_path'    => [__DIR__.'/../cache/doctrine'],
        ]
    ],
];
