<?php

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(__DIR__.'/classes/report.php');

// Check permissions
require_login();
require_capability('local/user_enroll_report:view', context_system::instance());

// Get parameters
$format = optional_param('download', '', PARAM_ALPHA);
$courseid = optional_param('courseid', 0, PARAM_INT);

// Check if course exists and is visible
if ($courseid) {
    $course = $DB->get_record('course', ['id' => $courseid, 'visible' => 1]);
}
if (!$course && $courseid !== 0) {
    // If the course does not exist or is not visible, show an error message
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'local_user_enroll_report'));
    \core\notification::error(get_string('coursenotfound', 'local_user_enroll_report', $courseid));
} else {
    // Set up the page
    if ($courseid === 1) {
        // If the course is the front page, show an error message
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('pluginname', 'local_user_enroll_report'));
        \core\notification::error(get_string('frontpagecourse', 'local_user_enroll_report', $courseid));
    } else {
        // Set the page URL based on the course ID
        if ($courseid !== 0) {
            $PAGE->set_url(new moodle_url('/local/user_enroll_report/index.php'), ['courseid' => $courseid]);
        } else {
            $PAGE->set_url(new moodle_url('/local/user_enroll_report/index.php'));
        }

        // Check if a download format is specified
        $formats = ['csv', 'excel', 'ods', 'json', 'html'];
        if ($format && in_array($format, $formats)) {
            // Clear output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Set content type and download
            $table = new user_enroll_report_table('user_enroll_report', $courseid);
            $table->define_baseurl($PAGE->url);

            // Define columns and headers for the report
            $columns = [
                'username',
                'firstname',
                'lastname',
                'coursename'
            ];

            $headers = [
                get_string('user', 'local_user_enroll_report'),
                get_string('firstname', 'local_user_enroll_report'),
                get_string('lastname', 'local_user_enroll_report'),
                get_string('coursename', 'local_user_enroll_report')
            ];

            $table->define_columns($columns);
            $table->define_headers($headers);

            // Download process
            if ($format === 'json') {
                $table->export_json($courseid);
            }
            else {
                if ($courseid) {
                    $table->is_downloading($format, 'user_enroll_report_course_' . $courseid);
                } else {
                    $table->is_downloading($format, 'user_enroll_report');
                }
                $table->out(0, false);
            }
            exit;
        }

        // If no download format is specified, display the report page
        // Set page title and heading
        $PAGE->set_title(get_string('pluginname', 'local_user_enroll_report'));
        $PAGE->set_heading(get_string('pluginname', 'local_user_enroll_report'));
        
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('pluginname', 'local_user_enroll_report'));

        // Create and display the report table
        $rowsperpage = get_config('local_user_enroll_report', 'rowsperpage');
        if (!$rowsperpage || $rowsperpage < 1) {
            $rowsperpage = 20; // Valor por defecto
        }

        $table = new user_enroll_report_table('user_enroll_report', $courseid);
        $table->define_baseurl($PAGE->url);
        $table->out($rowsperpage, true);

        // Display download options
        echo '<div class="mt-3">';
        foreach ($formats as $format) {
            $url = new moodle_url($PAGE->url, ['download' => $format]);
            echo html_writer::link($url, get_string("download{$format}fmt", 'local_user_enroll_report', $format), ['class' => 'btn btn-secondary mx-2']);
        }
        echo '</div>';
    }
}

// Display footer
echo $OUTPUT->footer();