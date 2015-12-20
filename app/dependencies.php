<?php
// DIC configuration

use Interop\Container\ContainerInterface;

$container = $app->getContainer();

//
// Slim3 Framework core handlers
//

// Arguments strategy
$container['foundHandler'] = function () {
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

// Custom error handling, catch all
$container['errorHandler'] = function (ContainerInterface $c) {
    return \Zaralab\Service\ErrorHandler::factory(0, $c);
};

//Custom 404 Not Found Handler
$container['notFoundHandler'] = function (ContainerInterface $c) {
    return \Zaralab\Service\ErrorHandler::factory(404, $c);
};

//Custom 405 Method Not Allowed Handler
$container['notAllowedHandler'] = function (ContainerInterface $c) {
    return \Zaralab\Service\ErrorHandler::factory(405, $c);
};

//
// Component integrations
//

// Doctrine
$container['em'] = function (ContainerInterface $c) {
    $settings = $c->get('settings');

    return Zaralab\Service\Doctrine::factory($settings, $c->get('DEBUG'));
};

// Twig
$container['view'] = function (ContainerInterface $c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new \Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new \Twig_Extension_Debug());

    return $view;
};

// monolog
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings');
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $debugLevel = $c->get('DEBUG') ? \Monolog\Logger::DEBUG : \Monolog\Logger::ERROR;
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], $debugLevel));

    return $logger;
};

// JMS Serializer
$container['serializer'] = function (ContainerInterface $c) {
    $settings = $c->get('settings');
    $serializer =
        \JMS\Serializer\SerializerBuilder::create()
            ->setCacheDir($settings['serializer']['cache'])
            ->setDebug($c->get('DEBUG'))
            ->build();

    return $serializer;
};

// Flash messages
$container['flash'] = function (ContainerInterface $c) {
    return new \Slim\Flash\Messages;
};

//
// Application services
//

// Member manager
$container['member.manager'] = function (ContainerInterface $c) {
    return new \Zaralab\Service\MemberManager(
        $c->get('em'),
        $c->get('security.encoder'),
        $c->get('logger')
    );
};

// API member controller
$container['ApiMemberController'] = function (ContainerInterface $c) {
    return new \Zaralab\Controller\Api\MemberController($c);
};

// Security service provider
$container->register(new \Zaralab\Service\Provider\SecurityProvider());

// Validator with doctrine annotation support
$container['validator'] = function (ContainerInterface $c) {
    return \Symfony\Component\Validator\Validation::createValidatorBuilder()
        ->enableAnnotationMapping()
        ->getValidator();
};