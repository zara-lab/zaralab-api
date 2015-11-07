<?php
/**
 * Project: zaralab
 * Filename: ApplicationController.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 05.11.15
 */

namespace Zaralab\Framework\Controller;


use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Environment;
use Slim\Router;
use Zaralab\Framework\Di\ContainerAware;

abstract class ApplicationController extends ContainerAware
{
    const URL_SCHEMA_NONE = 'none';
    const URL_SCHEMA_AUTO = 'auto';
    const URL_SCHEMA_HTTP = 'http';
    const URL_SCHEMA_HTTPS = 'https';

    /**
     * Constructor.
     *
     * @param ContainerInterface|null $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    /**
     * Generate url.
     *
     * @param string $name route name
     * @param array $parameters route parameters
     * @param array $query addittional query vars
     * @param string $schema type of url to be generated
     *
     * @return string generated url
     */
    public function url($name, $parameters = array(), $query, $schema = ApplicationController::URL_SCHEMA_NONE)
    {
        /** @var Router $router */
        $router = $this->container->get('router');

        return $this->getRequestUri($schema).$router->pathFor($name, $parameters, $query);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function jsonResponse(ResponseInterface $response)
    {
        return $response->withHeader('Content-type', 'application/json');
    }

    /**
     * Get Monolog logger shorthand.
     *
     * @return Logger
     */
    public function getMonolog()
    {
        return $this->get('logger');
    }

    /**
     * Get Doctrine Entity Manager shorthand.
     *
     * @return EntityManager
     */
    public function getDoctrine()
    {
        return $this->get('em');
    }

    /**
     * Check if the service id is defined.
     *
     * @param string $id The service id
     * @return bool true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a service.
     *
     * @param string $id The service id
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * HTTP schema, port and domain. Here till Slim3 implements
     * a router method to generate full URLs.
     * @return string
     */
    protected function getRequestUri($schema)
    {
        if ($schema == static::URL_SCHEMA_NONE) {
            return '';
        }

        /** @var Environment $env */
        $env = $this->get('environment');

        if ($schema != static::URL_SCHEMA_AUTO) {
            // Use it
        } elseif (($env->get('HTTPS') && $env->get('HTTPS') !== 'off')
            || (int) $env->get('SERVER_PORT') == 443
        ) {
            $schema = static::URL_SCHEMA_HTTPS;
        } elseif (($env->get('HTTP_X_FORWARDED_PROTO') && $env->get('HTTP_X_FORWARDED_PROTO') == 'https')
            || ($env->get('HTTP_X_FORWARDED_SSL') && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'))
        {
            $schema = static::URL_SCHEMA_HTTPS;
        } else {
            $schema = static::URL_SCHEMA_HTTP;
        }

        return $schema.'://'.$env->get('HTTP_HOST');
    }
}