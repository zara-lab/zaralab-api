<?php
/**
 * Project: zaralab
 * Filename: ErrorHandler.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 11.11.15
 */

namespace Zaralab\Service;

use Exception;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Body;
use Slim\Views\Twig;
use Zaralab\Exception\NotAllowedException;
use Zaralab\Exception\ResourceNotFoundException;
use Zaralab\Utils\ErrorHelper;

class ErrorHandler
{
    protected $displayErrorDetails;
    /**
     * Handler configuration array:
     *
     * 'html_template_path': path to Twig template, relative to the Twig template folder
     * 'xml_template_path': path to Twig template, relative to the Twig template folder
     * 'default_content_type': default content type
     * 'allowed_content_types': allowed content types
     * 'json_encode_options': options to be passed to json_encode
     * 'exceptions_with_code'
     * 'exceptions_without_code'
     * 'exceptions_with_messages': Exceptions list, @see ErrorHelper
     *
     * @var array
     */
    protected $options = [
        'html_template_path' => null,
        'xml_template_path' => null,
        'default_content_type' => 'text/html',
        'json_encode_options' => JSON_PRETTY_PRINT,
    ];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var callable
     */
    protected $contentTypeCallable;

    /**
     * Constructor.
     *
     * @param Twig $view Twig view
     * @param LoggerInterface $logger PSR logger
     * @param boolean $displayErrorDetails Set to true to display full details
     * @param array $options handler options
     * @param callable $contentTypeCallable callable to force response content type
     */
    public function __construct(
        $displayErrorDetails = false,
        LoggerInterface $logger = null,
        Twig $view = null,
        array $options = null,
        callable $contentTypeCallable = null
    ) {
        $this->displayErrorDetails = (bool)$displayErrorDetails;
        $this->logger = $logger;
        $this->view = $view;

        $options = $options ?: [];
        $this->options = array_merge($this->options, $options);

        if (!isset($this->options['allowed_content_types'])) {
            $this->options['allowed_content_types'] = ['text/html', 'application/json', 'application/xml', 'text/html'];
        }
        if (isset($options['exceptions_without_code'])) {
            $this->options['exceptions_without_code'] = $options['exceptions_without_code'];
        }
        if (isset($options['exceptions_with_code'])) {
            $this->options['exceptions_with_code'] = $options['exceptions_with_code'];
        }
        if (isset($options['exceptions_with_messages'])) {
            $this->options['exceptions_with_messages'] = $options['exceptions_with_messages'];
        }

        $this->contentTypeCallable = $contentTypeCallable;
    }


    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $exception)
    {
        $helper = new ErrorHelper(
            $exception,
            $this->option('exceptions_with_code'),
            $this->option('exceptions_without_code'),
            $this->option('exceptions_with_messages')
        );

        $helper->setKnownTypes($this->option('allowed_content_types'));

        // Detect content type from header
        $response = $response->withHeader('Content-type', $helper->detectContentType($request));

        // Check for passed content-type callable in options
        if ($this->contentTypeCallable) {
            $response = call_user_func($this->contentTypeCallable, $request, $response);
        }
        $contentType = $response->getHeader('Content-type')[0];

        // Detect status code from the exception
        $statusCode = $helper->detectStatusCode();

        // Use view if available
        $view = null;
        if ($this->view) {
            $view = function ($errorData, $template) {
                return $this->view->fetch($template, $errorData);
            };
        }

        $output = '';
        switch ($contentType) {
            case 'application/json':
                $output = $helper->renderJsonErrorResponse(
                    $this->displayErrorDetails,
                    $this->option('json_encode_options')
                );
                break;

            case 'text/xml':
            case 'application/xml':
                $output = $helper->renderXmlErrorResponse(
                    $this->displayErrorDetails,
                    $view,
                    $this->option('xml_template_path')
                );
                break;

            case 'text/html':
                $output = $helper->renderHtmlErrorResponse(
                    $this->displayErrorDetails,
                    $view,
                    $this->option('html_template_path')
                );
                break;
        }

        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($output);

        return $response
            ->withStatus($statusCode)
            ->withBody($body);
    }

    /**
     * @param $name
     * @param mixed $default
     * @return mixed
     */
    public function option($name, $default = null)
    {
        return !empty($this->options[$name]) ? $this->options[$name] : $default;
    }

    public static function factory($code, ContainerInterface $container)
    {
        // register middleware with the error handler as it breaks the middleware chain, check!
        $detect = new ContentTypeMiddleware($container['settings']['content_type.middleware']);

        // Initialize the handler, check!
        $handler = new ErrorHandler(
            $container->get('DEBUG'),
            $container->get('logger'),
            $container->get('view'),
            $container->get('settings')['errorHandler'],
            function($request, $response) use ($detect) {
                return $detect->execute($request, $response);
            }
        );

        // The generic core error handler, check && burn baby burn!
        if (!$code) {
            return $handler;
        }

        return function ($request, $response, $methods = null) use ($container, $code, $detect, $handler) {
            // pre-render the message, check!
            if ($code == 405) {
                if ($container->get('DEBUG')) {
                    $message = sprintf('Method not allowed, must be one of "%s"', implode(', ', $methods));
                } else {
                    $message = 'Method not allowed';
                }

                // prepare the 405 exception, check!
                $exception = new NotAllowedException($message);
            } else {
                // prepare the 404 exception, check!
                $exception = new ResourceNotFoundException('Requested resource not found');
            }

            return $handler($request, $response, $exception);
        };
    }

}