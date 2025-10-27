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
 * Feature support.
 */
function tabset_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES: return true; // allow manual completion
        case FEATURE_BACKUP_MOODLE2: return true;
        case FEATURE_NO_VIEW_LINK: return true;
        default: return null;
    }
}

/**
 * Add a new tabset instance.
 */
function tabset_add_instance($data, $mform = null) {
    global $DB;

    $record = new stdClass();
    $record->course      = $data->course;
    $record->name        = $data->name;
    $record->templateid  = $data->templateid;
    $record->intro       = $data->intro ?? null;
    $record->introformat = $data->introformat ?? FORMAT_HTML;
    $record->tabcontents = $data->tabcontents ?? '[]';
    $record->timecreated = $record->timemodified = time();

    // 1️⃣ Create DB record first so a context can exist.
    $id = $DB->insert_record('tabset', $record);

    // 2️⃣ Get proper module context.
    $cmid = $data->coursemodule;
    $context = \context_module::instance($cmid);

    // 3️⃣ Move any files from course context → module context.
    $oldcontext = \context_course::instance($data->course);
    $fs = get_file_storage();

    // Move all files in the course context area to the new module context.
    $fs->move_area_files_to_new_context($oldcontext->id, $context->id, 'mod_tabset', 'tabcontent');

    // 4️⃣ Update JSON text to ensure @@PLUGINFILE@@ links are correct.
    $contents = json_decode($record->tabcontents, true);
    $fixed = [];
    foreach ($contents as $i => $html) {
        $fixed[$i] = file_rewrite_pluginfile_urls(
            $html,
            'pluginfile.php',
            $context->id,
            'mod_tabset',
            'tabcontent',
            $i
        );
    }

    $record->id = $id;
    $record->tabcontents = json_encode($fixed);
    $DB->update_record('tabset', $record);

    return $id;
}



/**
 * Update an existing tabset instance.
 */
function tabset_update_instance($data, $mform = null) {
    global $DB;
    $record = $DB->get_record('tabset', array('id' => $data->instance), '*', MUST_EXIST);

    $record->name        = $data->name;
    // Template cannot be changed after creation to avoid drift.
    $record->intro       = $data->intro ?? null;
    $record->introformat = $data->introformat ?? 1;

    $tabcontents = array();
    if (!empty($data->tabcontent) && is_array($data->tabcontent)) {
        foreach ($data->tabcontent as $idx => $editor) {
            if (is_array($editor) && isset($editor['text'])) {
                $tabcontents[] = $editor['text'];
            } else {
                $tabcontents[] = (string)$editor;
            }
        }
    }
    $record->tabcontents = json_encode($tabcontents);
    $record->timemodified = time();

    $DB->update_record('tabset', $record);
    return true;
}

/**
 * Delete a tabset instance.
 */
function tabset_delete_instance($id) {
    global $DB;
    if (!$record = $DB->get_record('tabset', array('id' => $id))) {
        return false;
    }
    $DB->delete_records('tabset', array('id' => $id));
    return true;
}

/**
 * Render inline on the course page.
 * This is called when viewing the course page (no view.php needed).
 */
function mod_tabset_cm_info_view(cm_info $cm) {
    global $DB, $PAGE;

    if (!$cm->uservisible) {
        return;
    }

    // Get records.
    $tabset = $DB->get_record('tabset', ['id' => $cm->instance], '*', MUST_EXIST);
    $template = $DB->get_record('tabset_templates', ['id' => $tabset->templateid], '*', MUST_EXIST);

    // Add context and template data for the renderer.
    $tabset->cmid = $cm->id;
    $tabset->titles = json_decode($template->tabs ?? '[]', true);
    $tabset->tabcontents = json_decode($tabset->tabcontents ?? '[]', true);

    // Get the module renderer.
    $renderer = $PAGE->get_renderer('mod_tabset');
    $output = $renderer->render_tabset($tabset);

    // Hide the title (label-like behaviour).
    // $cm->name = '';
    $cm->set_content($output);
}


/**
 * Disallow changing template after creation by locking the selector on edit.
 */
function tabset_coursemodule_standard_elements($formwrapper, $mform) {
    // No-op: we use mod_form to disable the template dropdown on update.
}

/**
 * Serves the files from the tabset file areas.
 *
 * This is required so embedded images (and other media) inserted into
 * the tab editors can be displayed properly through pluginfile.php.
 *
 * File areas used:
 * - 'tabcontent' : per-tab editor content.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context  $context
 * @param string   $filearea
 * @param array    $args
 * @param bool     $forcedownload
 * @param array    $options
 * @return bool false if file not found, otherwise does not return (sends file)
 */
function mod_tabset_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    require_login($course, true, $cm);

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    if ($filearea !== 'tabcontent') {
        return false;
    }

    $itemid   = array_shift($args);             // e.g. 0

    $filename = array_pop($args);               // e.g. Slide3.jpeg
    // ✅ Normalise the filepath correctly
    $filepath = empty($args) ? '/' : '/' . implode('/', $args) . '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_tabset', 'tabcontent', $itemid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        debugging("File not found: context=$context->id, itemid=$itemid, filepath=$filepath, filename=$filename", DEBUG_DEVELOPER);
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

