<?php

use Behat\RestTestingContext\BaseContext;
use GuzzleHttp\ClientInterface;

/**
 * Defines application features from the specific context.
 */
class ApiContext extends BaseContext implements Behat\WebApiExtension\Context\ApiClientAwareContext
{
    const PRINT_HOOKS = false;

    private $client;

    use DatabaseTrait;

    /**
     * @inheritDoc
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }


}
