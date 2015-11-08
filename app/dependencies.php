<?php
// DIC configuration

use Interop\Container\ContainerInterface;

$container = $app->getContainer();

// Arguments strategy
$container['foundHandler'] = function() {
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

// Custom error handlnig - TODO move to class
$container['errorHandler'] = function ($c) {

    return function ($request, $response, $exception) use ($c) {
        /** @var \Slim\Http\Response $response */
        $response = $c['response'];
        $request = $c['request'];

        $response = $response->withStatus(500);
        $errTitle = 'Error';
        $errMessage = 'Internal Server Error';
        $code = $exception->getCode() ?: 1000; // unknown code if no exception code
        $contentType = 'text/html';

        if ($exception instanceof \Zaralab\Exception\ResourceNotFoundException) {
            $response = $response->withStatus(404);
            $errMessage = $exception->getMessage();
        }

        $error = ['error' => [ 'title' => $errTitle, 'message' => $errMessage, 'code' => $code ]];
        if ($c->get('DEBUG')) {
            $error['error']['exception'][] = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
            $_exception = $exception;

            while ($_exception = $_exception->getPrevious()) {
                $error['error']['exception'][] = [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => explode("\n", $exception->getTraceAsString()),
                ];
            }
        }

        if (strpos($request->getUri()->getPath(), '/api/') !== false) {
            $contentType = 'application/json';
            $output = json_encode($error);
        } else {
            if (!$c->get('DEBUG')) {
                /** @var \Slim\Views\Twig $view */
                $view = $c->get('view');
                $output = $view->fetch('error.twig', $error);
            } else {
                $handler = new \Slim\Handlers\Error();
                return $handler($request, $response, $exception);
            }

        }

        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $body->write($output);

        return $response
            ->withHeader('Content-type', $contentType)
            ->withBody($body);
    };
};

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
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Flash messages
$container['flash'] = function (ContainerInterface $c) {
    return new \Slim\Flash\Messages;
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
        JMS\Serializer\SerializerBuilder::create()
            ->setCacheDir($settings['serializer']['cache'])
            ->setDebug($c->get('DEBUG'))
            ->build();

    return $serializer;
};

// Member manager
$container['member.manager'] = function (ContainerInterface $c) {
    return new Zaralab\Service\MemberManager($c->get('em'), $c->get('logger'));
};

// API member controller
$container['ApiMemberController'] = function (ContainerInterface $c) {
    return new Zaralab\Controller\Api\MemberController($c);
};
