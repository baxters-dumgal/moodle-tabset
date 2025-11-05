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

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_tabset_mod_form extends moodleform_mod {

    function definition() {
        global $DB, $PAGE, $CFG;

        $mform = $this->_form;

        // General settings.
        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addHelpButton('name', 'name', 'mod_tabset');

        $this->standard_intro_elements();

        // Template selection.
        $templates = $DB->get_records_menu('tabset_templates', null, 'id', 'id, name');
        $mform->addElement('select', 'templateid', get_string('template', 'mod_tabset'), $templates);
        $mform->addRule('templateid', null, 'required', null, 'client');
        $mform->addHelpButton('templateid', 'templateid', 'mod_tabset');

        // ✅ Define editor options to allow file uploads.
        $editoroptions = [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $CFG->maxbytes,
            'trusttext' => true,
            'context' => $this->context,
            'subdirs' => 0,
        ];

        // Preload editors for maximum tabs (7 for Metaskills).
        for ($i = 0; $i < 7; $i++) {
            $mform->addElement('editor', "tabcontent[$i]", "Tab " . ($i+1), null, $editoroptions);
            $mform->setType("tabcontent[$i]", PARAM_RAW);
        }

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();

        // Pass templates + titles to JS.
        $alltemplates = [];
        foreach ($DB->get_records('tabset_templates') as $tpl) {
            $alltemplates[$tpl->id] = json_decode($tpl->tabs, true);
        }

        $PAGE->requires->js_call_amd('mod_tabset/form', 'init', [$alltemplates]);
    }

 function data_preprocessing(&$default_values) {
    global $DB;

    // ✅ Ensure context is set (important when adding a new instance).
    $context = $this->context ?? null;
    if (!$context && !empty($this->current->coursemodule)) {
        $context = \context_module::instance($this->current->coursemodule);
    } elseif (!$context) {
        $context = \context_course::instance($this->current->course);
    }

    $editoroptions = [
        'maxfiles'  => EDITOR_UNLIMITED_FILES,
        'maxbytes'  => 0,
        'trusttext' => true,
        'context'   => $context,
        'subdirs'   => 0,
    ];

    if (!empty($this->current->tabcontents)) {
        $contents = json_decode($this->current->tabcontents, true);

        foreach ($contents as $i => $html) {
            // ✅ Each tab gets its own draft itemid.
            $draftid = file_get_submitted_draft_itemid("tabcontent[$i]");

            $default_values["tabcontent[$i]"] = [
                'text'   => file_prepare_draft_area(
                    $draftid,
                    $context->id,
                    'mod_tabset',
                    'tabcontent',
                    0,              // ✅ Use tab index as itemid.
                    $editoroptions,
                    $html
                ),
                'format' => FORMAT_HTML,
                'itemid' => $draftid
            ];
        }
    }
}


function get_data() {
    global $CFG, $COURSE;

    $data = parent::get_data();
    if ($data) {
        $editoroptions = [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $CFG->maxbytes,
            'trusttext' => true,
            'context' => $this->context,
            'subdirs' => 0,
        ];

        // Use course context if module context not yet available.
        $savecontext = ($this->context && $this->context->contextlevel == CONTEXT_MODULE)
            ? $this->context
            : \context_course::instance($COURSE->id);

        $contents = [];
        if (!empty($data->tabcontent)) {
            foreach ($data->tabcontent as $i => $content) {
                $savedtext = file_save_draft_area_files(
                    $content['itemid'],
                    $savecontext->id,
                    'mod_tabset',
                    'tabcontent',
                    0,
                    $editoroptions,
                    $content['text']
                );
                $contents[$i] = $savedtext;
            }
        }
        $data->tabcontents = json_encode($contents);
    }
    return $data;
}

}
