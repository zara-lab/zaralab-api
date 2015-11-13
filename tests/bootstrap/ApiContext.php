<?php

use GuzzleHttp\Client;
use Zaralab\Behat\ApiClientContext;
use Zaralab\Behat\Traits as ZaralabTraits;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Defines application features from the specific context.
 */
class ApiContext extends ApiClientContext implements SnippetAcceptingContext
{
    const PRINT_HOOKS = false;

    use DatabaseTrait;
    use ZaralabTraits\AuthenticateContextTrait;
    use ZaralabTraits\GenericApiContext;

    /**
     * ApiContext constructor.
     *
     * Client config:
     *
     *     $client = new Client([
     *         'base_url' => [
     *              'http://www.foo.com/{version}/',
     *              ['version' => '123']
     *          ],
     *         'defaults' => [
     *             'timeout'         => 10,
     *             'allow_redirects' => false,
     *             'proxy'           => '192.168.16.1:10'
     *         ]
     *     ]);
     *
     * @param $base_url
     * @param int $version
     * @param array $options
     */
    public function __construct($base_url, $version = 1, $options = [])
    {
        $options['base_url'] = $base_url;
        $this->client = new Client($options);
    }

    /**
     * Prints last response body.
     *
     * @Then print last response
     */
    public function printResponse()
    {
        $request = $this->request;
        $response = $this->response;

        echo sprintf(
            "%s %s => %d:\n%s",
            $request->getMethod(),
            $request->getUrl(),
            $response->getStatusCode(),
            $response->getBody()
        );
    }

}
