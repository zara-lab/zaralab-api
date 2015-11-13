<?php
/**
 * Project: zaralab
 * Filename: AuthenticateContextTrait.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 13.11.15
 */

namespace Zaralab\Behat\Traits;


/**
 * Authenticate/authorize steps
 */
trait AuthenticateContextTrait
{

    /**
     * @Given /^I authenticate as "([^"]*)" with password "([^"]*)"$/
     */
    public function iAuthenticateAsWithPasswordSecret($email, $pasword)
    {
        $bodyOption = array(
            'body' => json_encode(['email' => $email, 'password' => $pasword]),
        );

        $this->prepareJsonContext();
        $url = $this->prepareUrl('authenticate');
        $this->request = $this->getClient()->createRequest('POST', $url, $bodyOption);
        $this->request->addHeaders($this->headers);

        $this->sendRequest();

        $data = $this->getResponseData();
        $this->authToken = isset($data['token']) ? $data['token'] : null;
    }

    /**
     * Prints last response body.
     *
     * @Then print authentication token
     */
    public function printToken()
    {
        echo sprintf(
            "Token %s",
            $this->authToken
        );
    }
}