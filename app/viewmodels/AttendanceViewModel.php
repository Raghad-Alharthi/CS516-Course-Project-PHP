<?php
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/User.php';

class AttendanceViewModel {
    public $student_id;
    public $full_name;
    public $is_present;

    public function __construct($student_id, $full_name, $is_present) {
        $this->student_id = $student_id;
        $this->full_name  = $full_name;
        $this->is_present = $is_present;
    }

    /**
     * Build an array of AttendanceViewModel for a lecture.
     * @return AttendanceViewModel[]
     */
    public static function forLecture($lectureId) {
        $rows = Attendance::findByLecture($lectureId);
        $list = [];
        foreach ($rows as $att) {
            $user = User::findById($att->student_id);
            $name = $user->first_name . ' ' . $user->last_name;
            $list[] = new self($att->student_id, $name, (bool)$att->is_present);
        }
        return $list;
    }
}
