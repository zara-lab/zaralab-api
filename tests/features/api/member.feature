@fixtures
Feature: Testing Member Rest service
  In order to retrieve and manage member information through the service
  as a service user
  I want to see if the service work as expected

  Scenario: Request active member - api_member_show route
    When I send a GET request to "/api/member/1"
    Then the response code should be 200
    And the response should contain json:
    """
    {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john.doe@example.com"
    }
    """
    And in the response there is no field called "phone"
    And in the response there is no field called "active"

  Scenario: Request inactive member - api_member_show route
    When I send a GET request to "/api/member/2"
    Then the response code should be 404
    And field "error" in the response should be "array"
    And the response should contain "Member not found"

  Scenario: Get a list of active members - api_member_list route
    When I send a GET request to "/api/member"
    Then the response code should be 200
    And the response should contain json:
    """
    {
      "total_items": "10"
    }
    """
    And field "items" in the response should be "array"

  Scenario: Create a member - api_member_create route

  Scenario: Update a member - api_member_update route

  Scenario: Patch a member - api_member_patch route

  Scenario: Delete a member - api_member_delete route