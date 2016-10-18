<?php
// This file is part of Moodle - http://moodle.org/
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
 * course_custom_fields block caps.
 *
 * @package    block_course_custom_fields
 * @copyright  Norbert Czirjak (czirjak.norbert@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2016090501;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2016012900;        // Requires this Moodle version
$plugin->component = 'block_course_custom_menu'; // Full name of the plugin (used for diagnostics)
$plugin->cron = 300;
