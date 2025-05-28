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
 * Plugin settings for the local_user_enroll_report plugin.
 * 
 * @package   local_user_enroll_report
 * @author    Agustín Robertazzi <robertazziagustin1806@gmail.com>
 * @copyright Agustín Robertazzi
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Check if the site configuration is available.
if ($hassiteconfig) {

    // Create a new settings page for the user enrollment report plugin.
    $settings = new admin_settingpage(
        'local_user_enroll_report_settings',
        get_string('settings', 'local_user_enroll_report')
    );

    // Add a setting for the number of rows per page in the report.
    $settings->add(new admin_setting_configselect(
        'local_user_enroll_report/rowsperpage',
        get_string('rowsperpage', 'local_user_enroll_report'),
        get_string('rowsperpagedesc', 'local_user_enroll_report'),
        10,
        [
            2 => '2',
            5 => '5',
            10 => '10',
            20 => '20',
        ]
    ));
    $ADMIN->add('localplugins', $settings);
}

// Add the plugin to the reports section in the admin settings.
if (has_capability('local/user_enroll_report:view', context_system::instance())) {
    $ADMIN->add('reports', new admin_externalpage(
        'local_user_enroll_report_main',
        get_string('pluginname', 'local_user_enroll_report'),
        new moodle_url('/local/user_enroll_report/index.php'),
    ));
}