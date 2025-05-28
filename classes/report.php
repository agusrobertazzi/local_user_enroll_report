<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

class user_enroll_report_table extends table_sql {

    public function __construct($uniqueid, $courseid = 0) {
        
        // Course ID for filtering
        $this->courseid = $courseid;

        // Initialize the table with a unique ID
        parent::__construct($uniqueid);

        // Set the table attributes
        $columns = [
            'id',
            'username',
            'firstname',
            'lastname',
            'coursename'
        ];
        $headers = [
            '',
            get_string('user', 'local_user_enroll_report'),
            get_string('firstname', 'local_user_enroll_report'),
            get_string('lastname', 'local_user_enroll_report'),
            get_string('coursename', 'local_user_enroll_report')
        ];
        $this->define_columns($columns);
        $this->define_headers($headers);

        $this->column_style('id', 'display', 'none');

        // Set SQL queries
        $this->set_sql(
            $this->get_fields_sql(),
            $this->get_from_sql(),
            $this->get_where_sql(),
            $this->get_params()
        );

        // Set count SQL
        $this->set_count_sql($this->get_count_sql(), $this->get_params());

        // Set table properties
        $this->sortable(true, 'username', SORT_ASC);
        $this->collapsible(false);
        $this->pageable(true);
    }

    /**
     * Get the SQL query to count the total number of records.
     *
     * @return string SQL query to count records
     */
    protected function get_count_sql() {
        return "SELECT COUNT(ue.id) FROM {user_enrolments} ue
                JOIN {user} u ON u.id = ue.userid
                JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0
                JOIN {course} c ON c.id = e.courseid AND c.visible = 1 WHERE " . $this->get_where_sql();
    }

    /**
     * Get the SQL fields to select for the report.
     *
     * @return string SQL fields to select
     */
    protected function get_fields_sql() {
        return "ue.id as enrolid, u.id, u.username, u.firstname, u.lastname, c.fullname as coursename, c.id as courseid";
    }

    /**
     * Get the SQL FROM clause for the report.
     *
     * @return string SQL FROM clause
     */
    protected function get_from_sql() {
        return "{user_enrolments} ue
                JOIN {user} u ON u.id = ue.userid
                JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0
                JOIN {course} c ON c.id = e.courseid AND c.visible = 1";
    }

    /**
     * Get the SQL WHERE clause for filtering records.
     *
     * @return string SQL WHERE clause
     */
    protected function get_where_sql() {
        $where = "u.deleted = 0 AND u.suspended = 0 AND u.username != ? AND u.id != ?";
        if ($this->courseid != 0) {
            $where .= " AND e.courseid = ?";
        }

        return $where;
    }

    /**
     * Get the parameters for the SQL query.
     *
     * @return array Parameters for the SQL query
     */
    protected function get_params() {
        $params = ['guest', 2];
        
        if ($this->courseid != 0) {
            $params[] = $this->courseid;
        }
        
        return $params;
    }

    /**
     * Get the SQL query to fetch the raw data for the report.
     * 
     * @param object $row Row object containing data
     * @return string SQL query to fetch raw data
     */
    public function col_id($row) {
        return $row->id;
    }

    /**
     * Get the username column for the report.
     *
     * @param object $row Row object containing user data
     * @return string HTML link to the user's profile or plain text if downloading
     */
    public function col_username($row) {
        if ($this->is_downloading()) {
            return s($row->username);
        } else {
            $userurl = new moodle_url('/user/profile.php', ['id' => $row->id]);
            return html_writer::link($userurl, s($row->username), ['class' => 'user-link']);
        }
    }

    /**
     * Get the first name column for the report.
     *
     * @param object $row Row object containing user data
     * @return string First name of the user
     */
    public function col_firstname($row) {
        return s($row->firstname);
    }

    /**
     * Get the last name column for the report.
     *
     * @param object $row Row object containing user data
     * @return string Last name of the user
     */
    public function col_lastname($row) {
        return s($row->lastname);
    }

    /**
     * Get the course name column for the report.
     *
     * @param object $row Row object containing course data
     * @return string HTML link to the course or plain text if downloading
     */
    public function col_coursename($row) {
        if ($this->is_downloading()) {
            return format_string($row->coursename);
        } else {
            $courseurl = new moodle_url('/course/view.php', ['id' => $row->courseid]);
            return html_writer::link($courseurl, format_string($row->coursename), ['class' => 'course' . 'course-link']);
        }
    }

    /**
     * Export the report data as JSON.
     *
     * @param int $courseid Course ID for filtering, default is 0 (all courses)
     */
    public function export_json($courseid = 0) {

        // Setup table without pagination or sorting
        $this->setup();
        $this->query_db(0, false);
        
        $records = [];
        foreach ($this->rawdata as $row) {
            $records[] = [
                'username' => $row->username,
                'firstname' => $row->firstname,
                'lastname' => $row->lastname,
                'coursename' => $row->coursename,
            ];
        }

        // HTTP Headers for JSON download
        $filename = 'user_enroll_report';
        if ($courseid != 0) {
            $filename .= '_course_' . $courseid;
        }
        $filename .= '.json';
        
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        
        // JSON output
        echo json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}