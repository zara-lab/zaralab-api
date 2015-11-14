@fixtures
Feature: Testing Member Rest service
  In order to retrieve and manage member information through the service
  as a service user
  I want to see if the service work as expected

  Scenario: Request active member - api_member_show route
    Given I am not authenticated
    When I send a GET request to "member/1"
      Then the response code should be 200
        And the response should be json array:
          | id          | 1                               |
          | first_name  | The Real                        |
          | last_name   | Chuck Norris                    |
          | email       | admin@example.com               |
        And in the response there is no field called "phone"
        And in the response there is no field called "enabled"
        And in the response there is no field called "salt"
        And in the response there is no field called "password"

  Scenario: Request inactive member - api_member_show route
    Given I am not authenticated
    When I send a GET request to "member/4"
      Then the response code should be 404
        And field "error" in the response should be "array"
        And the response should contain "Member not found"

  Scenario: Get a list of active members - api_member_list route
    Given I am not authenticated
    When I send a GET request to "member"
      Then the response code should be 200
        And field "total_items" in the response should be "integer"
        And field "items" in the response should be "array"
        And in the response there is no field called "phone"
        And in the response there is no field called "enabled"
        And in the response there is no field called "salt"
        And in the response there is no field called "password"
