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
        // Define each element of your structure.
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

        // Define sources.
        $tabset->set_source_table('tabset', ['id' => backup::VAR_ACTIVITYID]);

        // ✅ Include editor file areas.
        $tabset->annotate_files('mod_tabset', 'intro', null);
        $tabset->annotate_files('mod_tabset', 'tabcontent', null);

        // Return the root element (tabset), wrapped into standard activity structure.
        return $this->prepare_activity_structure($tabset);
    }
}

