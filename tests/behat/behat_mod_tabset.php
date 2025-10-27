<?php
// This file is part of Moodle - http://moodle.org/
//
// Copyright © 2025 Dumfries and Galloway College and contributors.
// Developed in collaboration with the Moodle community and ChatGPT (OpenAI).
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * Behat steps definitions for mod_tabset.
 *
 * @package   mod_tabset
 */
class behat_mod_tabset extends behat_base {

    /**
 * Checks that specific text is visible inside the Tabset activity container.
 *
 * @Then /^I should see "(?P<text>(?:[^"]|\\")*)" within "(?P<activityname>(?:[^"]|\\")*)"$/
 * @param string $text
 * @param string $activityname
 */
public function i_should_see_within($text, $activityname) {
    $activityxpath = "//li[contains(@class,'activity') and .//span[contains(text(),'{$activityname}')]]";
    $this->execute('behat_general::assert_element_contains_text', [$activityxpath, $text]);
}
  
}
