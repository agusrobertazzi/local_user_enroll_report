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
 * Plugin functions for the local_user_enroll_report plugin.
 * 
 * @package   local_user_enroll_report
 * @author    Agustín Robertazzi <robertazziagustin1806@gmail.com>
 * @copyright Agustín Robertazzi
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Add the plugin to the sidebar navigation in the course context.
function local_user_enroll_report_extend_navigation_course($navigation, $course, $context) {
    if (!has_capability('local/user_enroll_report:view', $context)) {
        return;
    }

    $url = new moodle_url('/local/user_enroll_report/index.php', ['courseid' => $course->id]);

    $navigation->add(
        get_string('viewreport', 'local_user_enroll_report'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'user_enroll_report',
        new pix_icon('i/report', '')
    );

}

// Add the plugin to the global navigation in the system context.
function local_user_enroll_report_extend_navigation(global_navigation $navigation) {
    global $PAGE;

    if (!has_capability('local/user_enroll_report:view', context_system::instance())) {
        return;
    }

    if ($PAGE->pagetype == 'course-view-topics' && !empty($PAGE->course->id)) {
        $url = new moodle_url('/local/user_enroll_report/index.php', ['courseid' => $PAGE->course->id]);
    } else {
        $url = new moodle_url('/local/user_enroll_report/index.php');
    }


    $node = $navigation->add(
        get_string('viewreport', 'local_user_enroll_report'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'user_enroll_report',
        new pix_icon('i/report', '')
    );

    $node->showinflatnavigation = true;
    $node->set_parent($navigation->find('home', global_navigation::TYPE_SETTING));
}