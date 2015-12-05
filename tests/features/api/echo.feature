Feature: Testing echo service
  In order to test API
  as a developer
  I'm able to send request to echo endpoint and retrieve same response as the request

  Scenario: Echo endpoint exists
    When I send a POST request to "echo"
    Then the response code should be 200

  Scenario: Echo endpoint returns request body
    When I set header "Content-type" with value "application/json"
    And I send a POST request to "echo" with body:
    """
    {
      "hello": "world"
    }
    """
    Then the response code should be 200
    And the response should contain json:
    """
    {
      "hello": "world"
    }
    """

  Scenario: Application responds to content type
    When I set header "Content-type" with value "application/x-www-form-urlencoded"
    And I send a POST request to "echo" with body:
    """
    hello=world
    """
    Then the response code should be 200
    And the response should contain json:
    """
    {
      "hello": "world"
    }
    """

  Scenario: Empty body without proper content type
    When I send a POST request to "echo" with body:
    """
    {
      "hello": "world"
    }
    """
    Then the response code should be 200
    And the response should be ""

