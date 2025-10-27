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
 * Defines the backup task for the tabset activity
 *
 * @package   mod_tabset
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/tabset/backup/moodle2/backup_tabset_stepslib.php');

class backup_tabset_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines the steps for the backup
     */
    protected function define_my_steps() {
        $this->add_step(new backup_tabset_activity_structure_step('tabset_structure', 'tabset.xml'));
    }

    /**
     * Encode content links to make them transportable
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Example of replacing view.php links
        $search = "/({$base}\/mod\/tabset\/view.php\?id=)([0-9]+)/";
        $content = preg_replace($search, '$@TABSETVIEWBYID*$2@$', $content);

        return $content;
    }
}
