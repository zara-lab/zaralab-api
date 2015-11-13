<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \Zaralab\Service\ContentTypeMiddleware($app->getContainer()['settings']['content_type.middleware']));