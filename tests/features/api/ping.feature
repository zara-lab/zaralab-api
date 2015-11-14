Feature: API ping

  Scenario: Ping API
    When I send a GET request to "ping"
    Then print last response
    Then the response code should be 200