<?php

/**
 * @author   Demin Yin <deminy@deminy.net>
 * @license  MIT license
 */

namespace Behat\RestTestingContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert;

/**
 * Rest context.
 */
class RestContext extends BaseContext
{
    /**
     * @Given /^the response should contain field "([^"]*)"$/
     * @param string $name
     * @return void
     * @throws \Exception
     */
    public function theResponseHasAField($name)
    {
        PHPUnit_Framework_Assert::assertArrayHasKey($name, $this->getResponseData());
    }

    /**
     * @Then /^in the response there is no field called "([^"]*)"$/
     * @param string $name
     * @return void
     * @throws \Exception
     */
    public function theResponseShouldNotHaveAField($name)
    {
        PHPUnit_Framework_Assert::assertArrayNotHasKey($name, $this->getResponseData());
    }

    /**
     * @Then /^field "([^"]+)" in the response should be "([^"]*)"$/
     * @param string $name
     * @param string $value
     * @return void
     * @throws \Exception
     */
    public function valueOfTheFieldEquals($name, $value)
    {
        PHPUnit_Framework_Assert::assertArrayHasKey($name, $this->getResponseData())
        && PHPUnit_Framework_Assert::assertEquals($value, $this->responseData[$name])
        ;
    }

    /**
     * @Then /^field "([^"]+)" in the response should be an? (int|integer) "([^"]*)"$/
     * @param string $name
     * @param string $type
     * @param string $value
     * @return void
     * @throws \Exception
     * @todo Need to be better designed.
     */
    public function fieldIsOfTypeWithValue($name, $type, $value)
    {
        PHPUnit_Framework_Assert::assertArrayHasKey($name, $this->getResponseData())
        && PHPUnit_Framework_Assert::assertEquals($value, $this->responseData[$name])
        ;

        switch (strtolower($type)) {
            case 'int':
            case 'integer':
                if (!preg_match('/^(0|[1-9]\d*)$/', $value)) {
                    throw new \Exception(
                        sprintf(
                            'Field "%s" is not of the correct type: %s!',
                            $name,
                            $type
                        )
                    );
                }
                // TODO: We didn't check if the value is as expected here.
                break;
            default:
                throw new \Exception('Unsupported data type: ' . $type);
                break;
        }
    }

    /**
     * @Given /^the response should be "([^"]*)"$/
     * @param string $string
     * @return void
     * @throws \Exception
     */
    public function theResponseShouldBe($string)
    {
        PHPUnit_Framework_Assert::assertSame($string, $this->getResponseBody());
    }

    /**
     * @Then /^response header field "([^"]*)" should be "([^"]*)"$/
     */
    public function responseHeaderFieldShouldBe($field, $value)
    {
        PHPUnit_Framework_Assert::assertSame($value, $this->getResponse()->getHeader($field));
    }

    /**
     * @Then /^there is no header field "([^"]*)"$/
     */
    public function responseHeaderShouldNotContainField($field)
    {
        PHPUnit_Framework_Assert::assertNull($this->getResponse()->getHeader($field));
    }

    /**
     * @Then /^the header field "([^"]*)" is empty$/
     */
    public function responseHeaderFieldShouldBeEmpty($field)
    {
        PHPUnit_Framework_Assert::assertEmpty($this->getResponse()->getHeader($field));
    }

    /**
     * @Given /^I am not authenticated$/
     */
    public function iAmNotAuthenticated()
    {

    }

    /**
     * Json response should equal to the data table
     *
     * @Then /^the response should be json array:$/
     */
    public function theResponseShouldBeJson(TableNode $json)
    {
        $etalon = array();

        foreach ($json->getRowsHash() as $key => $val) {
            $etalon[$key] = $val;
        }

        $actual = $this->getResponse()->json();

        PHPUnit_Framework_Assert::assertEquals(count($etalon), count($actual));
        foreach ($etalon as $key => $needle) {
            PHPUnit_Framework_Assert::assertArrayHasKey($key, $actual);
            PHPUnit_Framework_Assert::assertEquals($etalon[$key], $actual[$key]);
        }
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return (string) $this->getResponse()->getBody();
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
