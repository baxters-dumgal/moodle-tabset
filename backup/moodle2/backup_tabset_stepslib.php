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
 * Backup steps for the tabset activity
 *
 * @package   mod_tabset
 */

defined('MOODLE_INTERNAL') || die();



class backup_tabset_activity_structure_step extends backup_activity_structure_step {




     protected function define_structure() {
        // 1️⃣ Define the root element for this activity.
        $tabset = new backup_nested_element('tabset', ['id'], [
            'course',
            'name',
            'templateid',
            'intro',
            'introformat',
            'tabcontents',
            'timecreated',
            'timemodified'
        ]);

        // 2️⃣ Define the data source — the main DB table.
        // Maps the activity ID being backed up to the DB record.
        $tabset->set_source_table('tabset', ['id' => backup::VAR_ACTIVITYID]);

        // 3️⃣ Annotate only the intro file area (safe, always exists).
        $tabset->annotate_files('mod_tabset', 'intro', null);
        $tabset->annotate_files('mod_tabset', 'tabcontent', null);
       
        // 4️⃣ Return the structure wrapped into the standard activity structure.
        return $this->prepare_activity_structure($tabset);
    }
}

