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
 * Restore steps for the tabset activity
 *
 * @package   mod_tabset
 */

defined('MOODLE_INTERNAL') || die();

class restore_tabset_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {
        // 1️⃣ Define one simple path: <activity><tabset>...</tabset></activity>
        $paths = [];
        $paths[] = new restore_path_element('tabset', '/activity/tabset');

        return $this->prepare_activity_structure($paths);

    }

    protected function process_tabset($data) {
        global $DB;

        // 2️⃣ Convert $data (array from XML) into an object.
        $data = (object)$data;
        $data->course = $this->get_courseid();

        // 3️⃣ Insert it into your module's main table.
        $newitemid = $DB->insert_record('tabset', $data);

        // 4️⃣ Apply mapping between old and new instance IDs.
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // 5️⃣ Restore standard intro files (if any).
        $this->add_related_files('mod_tabset', 'intro', null);
        // Restore all tabcontent files (single itemid = null).
        $this->add_related_files('mod_tabset', 'tabcontent', null);
    }
}
