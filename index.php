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

require('../../config.php');

$id = required_param('id', PARAM_INT); // Course id.

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$PAGE->set_url('/mod/tabset/index.php', array('id' => $id));
$PAGE->set_title(get_string('modulenameplural', 'tabset'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'tabset'));

if (!$tabsets = get_all_instances_in_course('tabset', $course)) {
    echo $OUTPUT->notification(get_string('none'));
    echo $OUTPUT->footer();
    exit;
}

$table = new html_table();
$table->head = array(get_string('name'), get_string('intro'));

foreach ($tabsets as $cm) {
    $name = format_string($cm->name);
    $link = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), $name);
    $intro = shorten_text(format_text($cm->intro, $cm->introformat), 140);
    $table->data[] = array($name, $intro);
}

echo html_writer::table($table);
echo $OUTPUT->footer();
