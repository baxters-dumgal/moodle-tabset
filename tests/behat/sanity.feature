@mod_tabset @javascript
Feature: Behat sanity check
  In order to confirm Behat is configured correctly
  As an administrator
  I need to verify that Moodle pages load

  Scenario: Open the login page
    Given I log in as "admin"
    And I am on site homepage
    Then I should see "Dashboard"
