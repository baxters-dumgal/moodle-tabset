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

/**
 * Defines the restore task for the tabset activity
 *
 * @package   mod_tabset
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/tabset/backup/moodle2/restore_tabset_stepslib.php');

class restore_tabset_activity_task extends restore_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Define the steps to perform during restore
     */
    protected function define_my_steps() {
        $this->add_step(new restore_tabset_activity_structure_step('tabset_structure', 'tabset.xml'));
    }

    /**
     * Define content decoding rules
     */
    public static function define_decode_contents() {
        $contents = [];

        // Decode URLs in both intro and tabcontents fields
        $contents[] = new restore_decode_content('tabset', ['intro', 'tabcontents'], 'tabset');

        return $contents;
    }

    /**
     * Define link decoding rules
     */
    public static function define_decode_rules() {
        $rules = [];
        $rules[] = new restore_decode_rule('TABSETVIEWBYID', '/mod/tabset/view.php?id=$1', 'course_module');
        return $rules;
    }

    /**
     * Define restore log rules
     */
    public static function define_restore_log_rules() {
        $rules = [];
        $rules[] = new restore_log_rule('tabset', 'add', 'view.php?id={course_module}', '{tabset}');
        $rules[] = new restore_log_rule('tabset', 'update', 'view.php?id={course_module}', '{tabset}');
        $rules[] = new restore_log_rule('tabset', 'view', 'view.php?id={course_module}', '{tabset}');
        return $rules;
    }
}
