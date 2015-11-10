@fixtures
Feature: Testing Member Rest service
  In order to retrieve and manage member information through the service
  as a registered service user
  I want to see if the service work as expected

  Scenario: Request JSON Web Token success - api_auth route
    Given I set header "Content-type" with value "application/json"
      And I send a POST request to "/api/authenticate" with values:
        | email    | chuck.norris.real@example.com |
        | password | secret                        |
    Then the response code should be 200
      And the response should contain field "token"
      And field "token" in the response should be "string"
      And response header field "X-Authenticated-With" should be "chuck.norris.real(at)example.com"

  Scenario: Request JSON Web Token password error - api_auth route
    When I set header "Content-type" with value "application/json"
    And I send a POST request to "/api/authenticate" with values:
      | email    | chuck.norris.real@example.com |
      | password | badPassword                   |
    Then the response code should be 400
    And in the response there is no field called "token"
    And field "error" in the response should be "array"
    And the header field "X-Authenticated-With" is empty
