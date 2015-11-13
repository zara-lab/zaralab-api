<?php
/**
 * Project: zaralab
 * Filename: ClientTrait.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 13.11.15
 */

namespace Zaralab\Behat;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Stream\Stream;

/**
 * Api Client Context base.
 * Based on
 *
 * WebApiContext (WebApiExtension)
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * RestContext (RestTestingExtension)
 * @author Demin Yin <deminy@deminy.net>
 */
class ApiClientContext
{
    /**
     * @var string
     */
    protected $authorization;

    /**
     * @var array
     */
    protected $placeHolders = [];

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * Store data used across different contexts and steps.
     *
     * @var array
     */
    protected static $data = array();

    /**
     * Get data by field name, or return all data if no field name provided.
     *
     * @param string $name Field name.
     * @return mixed
     * @throws \Exception
     */
    public static function get($name = null)
    {
        if (!isset($name)) {
            return self::$data;
        } else {
            if (static::exists($name)) {
                return self::$data[$name];
            } else {
                throw new \Exception("Requested data field '{$name}' not exist.");
            }
        }
    }

    /**
     * Set value on given field name.
     *
     * @param string $name Field name.
     * @param mixed $value Field value.
     * @return void
     */
    public static function set($name, $value)
    {
        self::$data[$name] = $value;
    }

    /**
     * Check if specified field name exists or not.
     *
     * @param string $name Field name.
     * @return boolean
     */
    public static function exists($name)
    {
        return array_key_exists($name, self::$data);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        return self::get($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return self::exists($name);
    }

    /**
     * @param string $name
     * @return mixed $value
     * @return $this
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        self::set($name, $value);

        return $this;
    }

    /**
     * Returns headers, that will be used to send requests.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Adds header
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    protected function addHeader($name, $value)
    {
        if (isset($this->headers[$name])) {
            if (!is_array($this->headers[$name])) {
                $this->headers[$name] = array($this->headers[$name]);
            }

            $this->headers[$name][] = $value;
        } else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * Removes a header identified by $headerName
     *
     * @param string $headerName
     *
     * @return $this
     */
    protected function removeHeader($headerName)
    {
        if (array_key_exists($headerName, $this->headers)) {
            unset($this->headers[$headerName]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * @return $this
     */
    protected function sendRequest()
    {
        try {
            $this->response = $this->getClient()->send($this->request);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();

            if (null === $this->response) {
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Prepare URL by replacing placeholders and trimming slashes.
     *
     * @param string $url
     *
     * @return string
     */
    protected function prepareUrl($url)
    {
        return ltrim($this->replacePlaceHolder($url), '/');
    }

    protected function prepareJsonContext()
    {
        $this->addHeader('Content-Type', 'application/json')
            ->addHeader('Accept', 'application/json');
    }

    /**
     * Sets place holder for replacement.
     *
     * you can specify placeholders, which will
     * be replaced in URL, request or response body.
     *
     * @param string $key   token name
     * @param string $value replace value
     */
    public function setPlaceHolder($key, $value)
    {
        $this->placeHolders[$key] = $value;
    }

    /**
     * Replaces placeholders in provided text.
     *
     * @param string $string
     *
     * @return string
     */
    protected function replacePlaceHolder($string)
    {
        foreach ($this->placeHolders as $key => $val) {
            $string = str_replace($key, $val, $string);
        }

        return $string;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return (string) $this->response->getBody();
    }

    /**
     * @param string $body
     * @return void
     */
    protected function setResponseBody($body)
    {
        $this->response->setBody(Stream::factory($body));
    }

    /**
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->decodeJson($this->getResponseBody());
    }

    /**
     * Decode JSON string.
     *
     * @param string $string A JSON string.
     * @return mixed
     * @throws \Exception
     * @see http://www.php.net/json_last_error
     */
    protected function decodeJson($string)
    {
        $json = json_decode($string, true);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $json;
                break;
            case JSON_ERROR_DEPTH:
                $message = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $message = 'Unknown error';
                break;
        }

        throw new \Exception('JSON decoding error: ' . $message);
    }
}