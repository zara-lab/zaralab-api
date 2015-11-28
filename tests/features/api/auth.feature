@fixtures
Feature: Testing JSON Web Token of the REST service ("api_auth" named route)
  In order to get access to protected areas of the service
  as a public visitor
  I'm able to retrieve JSON Web Token with correct credentials only

  Scenario: Credentials success
    When I authenticate as "member@example.com" with password "secret"
      Then the response code should be 200
        And the response should contain field "token"
        And field "token" in the response should be "string"
        And in the response there is no field called "error"
        And print authentication token

  Scenario: Disabled member access is denied
    When I authenticate as "disabled@example.com" with password "secret"
      Then the response code should be 403
        And in the response there is no field called "token"
        And field "error" in the response should be "array"
        And the response should contain "Account is disabled"

  Scenario: Credentials with wrong password
    When I authenticate as "member@example.com" with password "bad password"
      Then the response code should be 400
        And in the response there is no field called "token"
        And field "error" in the response should be "array"

  Scenario: Credentials with wrong email
    When I authenticate as "bad member" with password "no matter the password"
      Then the response code should be 400
      And in the response there is no field called "token"
      And field "error" in the response should be "array"

  Scenario: Empty credentials
    When I authenticate as "" with password "no matter the password"
      Then the response code should be 400
        And in the response there is no field called "token"
        And field "error" in the response should be "array"
    When I authenticate as "chuck.norris.real@example.com" with password ""
      Then the response code should be 400
        And in the response there is no field called "token"
        And field "error" in the response should be "array"
    When I authenticate as "" with password ""
      Then the response code should be 400
        And in the response there is no field called "token"
        And field "error" in the response should be "array"