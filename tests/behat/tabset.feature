@mod_tabset @javascript
Feature: A teacher can add and view a Tabset activity
  In order to display tabbed information
  As a teacher
  I need to create a Tabset activity and see its tabs on the course page

  Scenario: Teacher adds a Tabset activity
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test Course | TC101 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | One      | teacher1@test.com |
    And the following "course enrolments" exist:
      | user     | course     | role    |
      | teacher1 | TC101      | editingteacher |
    Given I log in as "teacher1"
    And I am on "Test Course" course homepage
    And I turn editing mode on
    When I add a "Tabset" to section "1"
    And I set the following fields to these values:
      | Name | Example Tabset |
      | Template | Basic |
    And I press "Save and return to course"
    Then I should see "Example Tabset" in the "section 1" of the course
