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

$plugin->component = 'mod_tabset';      // Full name of the plugin (used for diagnostics).
$plugin->version   = 2025100619;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires = 2024100700;         // Requires Moodle 5.0 (adjust if you need earlier support).
$plugin->maturity  = MATURITY_ALPHA;    // This is an alpha version.
$plugin->release   = '0.2.0';           // Human-readable release name.
