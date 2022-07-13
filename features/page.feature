Feature: Test that basic pages are reachable and interactive

  Scenario: Homepage is reachable and contains text
    Given I am on "/"
    Then the response should contain "DELOS"

  Scenario: Homepage is reachable and cookiebanner can be dismissed
    Given I am on "/"
    And I wait 1 second
    Then I screenshot "startpage"
    And I press "Alle Funktionen akzeptieren"
    Then I should not see "Alle Funktionen akzeptieren"

    # last test from the event - should fail
    Then I compare "startpage"
