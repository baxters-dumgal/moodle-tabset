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

function xmldb_tabset_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025091709) {
        $templates = [
            ['name' => 'Basic', 'tabs' => json_encode(['Welcome', 'About', 'Contacts'])],
            ['name' => 'Course', 'tabs' => json_encode(['Welcome', 'About', 'Outcomes', 'Schedule', 'Assessment', 'Contacts'])],
            ['name' => 'Metaskills', 'tabs' => json_encode(['Welcome', 'About', 'Outcomes', 'Metaskills', 'Schedule', 'Assessment', 'Contacts'])]
        ];
        foreach ($templates as $tpl) {
            if (!$DB->record_exists('tabset_templates', ['name' => $tpl['name']])) {
                $rec = (object) $tpl;
                $DB->insert_record('tabset_templates', $rec);
            }
        }

        upgrade_mod_savepoint(true, 2025091709, 'tabset');
    }

    return true;
}
