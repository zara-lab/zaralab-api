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

        // Error handler
        'errorHandler' => [
            'html_template_path'       => 'error.html.twig',
            'xml_template_path'        => 'error.xml.twig',
            'default_content_type'     => 'application/json',
            'allowed_content_types'    => ['text/html', 'application/json'],
            'json_encode_options'      => JSON_PRETTY_PRINT,
            'exceptions_with_code'     => [
                'Symfony\Component\Security\Core\Exception\AuthenticationException' => 401
            ],
            'exceptions_without_code'  => [
                'Zaralab\Exception\ResourceNotFoundException' => 404,
                'Zaralab\Exception\NotAllowedException'       => 405,
            ],
            'exceptions_with_messages' => [
                'Zaralab\Exception\ResourceNotFoundException',
                'Zaralab\Exception\NotAllowedException',
                'Symfony\Component\Security\Core\Exception\AuthenticationException',
            ],
        ],

        // Content type middleware settings
        'content_type.middleware' => [
            'application/json' => [
                'path'   => [ '/api' ],
                'ignore' => [  ]
            ]
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
