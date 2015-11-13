<?php
/**
 * Project: zaralab
 * Filename: ExceptionHelper.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 11.11.15
 */

namespace Zaralab\Utils;

use Psr\Http\Message\ServerRequestInterface;

class ErrorHelper
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * Known handled content types
     *
     * @var array
     */
    protected $knownContentTypes = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/html',
    ];

    /**
     * Check exception code when exception class matches and use it,
     * fallback to corresponding value.
     *
     * @var array
     */
    protected $exceptionsWithCode = [];

    /**
     * Don't check the exception code when exception class matches,
     * just use the corresponding value.
     *
     * @var array
     */
    protected $exceptionsWithoutCode = [];

    /**
     * Use exception messages as error message for matches from this list.
     *
     * @var array
     */
    protected $exceptionsWithMessages = [];


    /**
     * ExceptionHelper constructor.
     *
     * @param \Exception $exception
     * @param array|null $exceptionsWithCode
     * @param array|null $exceptionsWithoutCode
     * @param array|null $exceptionsWithMessages
     */
    public function __construct(\Exception $exception, array $exceptionsWithCode = null, array $exceptionsWithoutCode = null, array $exceptionsWithMessages = null)
    {
        $this->exception = $exception;

        if (null !== $exceptionsWithCode) {
            $this->exceptionsWithCode = $exceptionsWithCode;
        }

        if (null !== $exceptionsWithoutCode) {
            $this->exceptionsWithoutCode = $exceptionsWithoutCode;
        }

        if (null !== $exceptionsWithMessages) {
            $this->exceptionsWithMessages = $exceptionsWithMessages;
        }
    }

    /**
     * @param array $types
     */
    public function setKnownTypes(array $types)
    {
        $this->knownContentTypes = $types;
    }

    /**
     * Try to detect HTTP status code
     * from the exception object.
     *
     * @return int HTTP status code
     */
    public function detectStatusCode()
    {
        foreach ($this->exceptionsWithCode as $e => $_code) {
            if (is_a($this->exception, $e)) {
                return $this->exception->getCode() ?: $_code;
            }
        }

        foreach ($this->exceptionsWithoutCode as $e => $_code) {
            if (is_a($this->exception, $e)) {
                return $_code;
            }
        }

        return 500;
    }

    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * @param ServerRequestInterface $request
     * @param string $default
     * @return string
     */
    public function detectContentType(ServerRequestInterface $request, $default = null)
    {
        $acceptHeader = $request->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $this->knownContentTypes);

        if (count($selectedContentTypes)) {
            return $selectedContentTypes[0];
        }

        return $default && in_array($default, $this->knownContentTypes) ? $default : 'text/html';
    }

    /**
     * Try to detect the generic error message
     * from the exception object.
     *
     * @return string
     */
    public function detectErrorMessage()
    {
        $errMessage = 'Internal Server Error';

        foreach ($this->exceptionsWithMessages as $e) {
            if (is_a($this->exception, $e)) {
                $errMessage = $this->exception->getMessage();
                // Symfony2 security component exceptions
                if(empty($errMessage) && method_exists($this->exception, 'getMessageKey')) {
                    return $this->exception->getMessageKey();
                }
                return $errMessage;
            }
        }

        return $errMessage;
    }

    /**
     * Build error data from exception.
     *
     * @param bool $debug
     *
     * @return array
     */
    public function buildErrorStack($debug)
    {
        $error = ['error' =>
            [
                'message' => $this->detectErrorMessage(),
                'code'    => $this->exception->getCode() ?: $this->detectStatusCode()
            ]
        ];

        return $this->applyDebugFromException($error, $debug);
    }

    /**
     * Render JSON error response
     *
     * @param bool $debug
     * @param int $jsonEncodeOptions
     *
     * @return string
     */
    public function renderJsonErrorResponse($debug, $jsonEncodeOptions = JSON_PRETTY_PRINT)
    {
        return json_encode($this->buildErrorStack($debug), $jsonEncodeOptions);
    }

    /**
     * Render HTML error response
     *
     * @param bool $debug
     * @param callable $view Template for Twig rendering
     * @param string $template Template for Twig rendering
     *
     * @return string
     */
    public function renderHtmlErrorResponse($debug, $view = null, $template = null)
    {
        return $this->renderErrorResponse('Html', $debug, $view, $template);
    }

    /**
     * Render HTML error response
     *
     * @param bool $debug
     * @param callable $view Template for Twig rendering
     * @param string $template Template for Twig rendering
     *
     * @return string
     */
    public function renderXmlErrorResponse($debug, $view = null, $template = null)
    {
        return $this->renderErrorResponse('Xml', $debug, $view, $template);
    }

    /**
     * Convert exception to array data
     *
     * @param array $errorStack
     * @param bool $debug
     *
     * @return array
     */
    protected function applyDebugFromException(array $errorStack, $debug)
    {
        if (!$debug) {
            return $errorStack;
        }
        $exception = $this->exception;

        $errorStack['error']['exception'][] = [
            'class' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString()),
        ];
        $_exception = $exception;

        while ($_exception = $_exception->getPrevious()) {
            $errorStack['error']['exception'][] = [
                'class' => get_class($exception),
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        return $errorStack;
    }

    /**
     * Render HTML error response
     *
     * @param string $type Html|Xml
     * @param bool $debug
     * @param callable $view Template for Twig rendering
     * @param string $template Template for Twig rendering
     *
     * @return string
     */
    protected function renderErrorResponse($type, $debug, $view = null, $template = null)
    {
        if (null === $view) {
            $that = $this;
            $view = function($data, $template) use ($that, $type) {
                $method = "fetch{$type}Template";
                return $that->$method($data, $template);
            };
        }

        if (!is_callable($view)) {
            throw new \InvalidArgumentException("View is not callable");
        }

        $error = $this->buildErrorStack($debug);

        return $view($error, $template);
    }

    /**
     * Fetch default HTML template
     *
     * @param array $data
     * @param $template
     * @return string
     */
    protected function fetchHtmlTemplate(array $data, $template)
    {
        if (isset($data['error']['exception'])) {
            $html = '<p>The application could not run because of the following error:</p>';

            foreach ($data['error']['exception'] as $i => $exception) {
                $html .= $i ? '<h2>Previous exception</h2>' : '<h2>Details</h2>';

                $html .= sprintf('<div><strong>Type:</strong> %s</div>', $exception['class']);

                if ($exception['code']) {
                    $html .= sprintf('<div><strong>Code:</strong> %s</div>', $exception['code']);
                }

                if ($exception['message']) {
                    $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlentities($exception['message'], null, 'UTF-8'));
                }

                if ($exception['file']) {
                    $html .= sprintf('<div><strong>File:</strong> %s</div>', $exception['file']);
                }

                if ($exception['line']) {
                    $html .= sprintf('<div><strong>Line:</strong> %s</div>', $exception['line']);
                }

                if ($exception['trace']) {
                    $html .= '<h2>Trace</h2>';
                    $html .= sprintf('<pre>%s</pre>', htmlentities(implode("\n", $exception['trace']), null, 'UTF-8'));
                }
            }
        } else {
            $html = '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';
        }

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
            "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
            "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
            "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
            htmlentities($data['error']['message'], null, 'UTF-8'),
            htmlentities($data['error']['message'], null, 'UTF-8'),
            $html
        );

        return $output;
    }

    /**
     * Fetch default XML template
     *
     * @param array $data
     * @param $template
     * @return string
     */
    protected function fetchXmlTemplate(array $data, $template)
    {
        $title = $this->createCdataSection($data['error']['message']);
        $code = $this->createCdataSection($data['error']['code']);

        $xml = "<error>\n  <message>$title</message>\n  <code>$code</code>\n";

        if (isset($data['error']['exception'])) {
            foreach ($data['error']['exception'] as $exception)  {
                $xml .= "  <exception>\n";
                $xml .= "    <type>" . $exception['class'] . "</type>\n";
                $xml .= "    <code>" . $exception['code'] . "</code>\n";
                $xml .= "    <message>" . $this->createCdataSection($exception['message']) . "</message>\n";
                $xml .= "    <file>" . $exception['file'] . "</file>\n";
                $xml .= "    <line>" . $exception['line'] . "</line>\n";
                $xml .= "    <trace>" . $this->createCdataSection(implode("\n", $exception['trace'])) . "</trace>\n";
                $xml .= "  </exception>\n";
            }
        }
        $xml .= "</error>";

        return $xml;
    }

    /**
     * Returns a CDATA section with the given content.
     *
     * @param  string $content
     * @return string
     */
    protected function createCdataSection($content)
    {
        return sprintf('<![CDATA[%s]]>', str_replace(']]>', ']]]]><![CDATA[>', $content));
    }
}