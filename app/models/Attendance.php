<?php
require_once __DIR__ . '/../config.php';

class Attendance {
    public $attendance_id;
    public $student_id;
    public $lecture_id;
    public $is_present;
    public $sick_leave_file;
    public $sick_leave_status;
    public $sick_leave_comment;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function record($studentId, $lectureId, $isPresent) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            INSERT INTO attendance (student_id, lecture_id, is_present, sick_leave_status)
            VALUES (?, ?, ?, "Pending")
        ');
        $stmt->execute([$studentId, $lectureId, $isPresent ? 1 : 0]);
        return new self([
            'attendance_id'     => $pdo->lastInsertId(),
            'student_id'        => $studentId,
            'lecture_id'        => $lectureId,
            'is_present'        => $isPresent,
            'sick_leave_status' => 'Pending'
        ]);
    }

    public static function findByLecture($lectureId) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM attendance WHERE lecture_id = ?');
        $stmt->execute([$lectureId]);
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }

    public static function findById(int $id): ?self {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM attendance WHERE attendance_id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public static function findRecord(int $lectureId, int $studentId): ?self {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'SELECT * FROM attendance WHERE lecture_id = ? AND student_id = ? LIMIT 1'
        );
        $stmt->execute([$lectureId, $studentId]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public static function findRequests(int $lectureId): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'SELECT * FROM attendance
             WHERE lecture_id = ? AND sick_leave_file IS NOT NULL
               AND sick_leave_file <> \'\''
        );
        $stmt->execute([$lectureId]);
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }

    public static function countAbsentForStudentInClass(int $studentId, int $classId): int {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM attendance a JOIN lectures l ON a.lecture_id = l.lecture_id
             WHERE a.student_id = ? AND l.class_id = ? AND a.is_present = 0'
        );
        $stmt->execute([$studentId, $classId]);
        return (int)$stmt->fetchColumn();
    }

    public static function findAbsencesForStudentClass(int $studentId, int $classId): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'SELECT a.*, l.lecture_datetime FROM attendance a
             JOIN lectures l ON a.lecture_id = l.lecture_id
             WHERE a.student_id = ? AND l.class_id = ? AND a.is_present = 0
             ORDER BY l.lecture_datetime DESC'
        );
        $stmt->execute([$studentId, $classId]);
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }
    
    public function save() {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            UPDATE attendance
            SET is_present = ?, sick_leave_file = ?, sick_leave_status = ?, sick_leave_comment = ?
            WHERE attendance_id = ?
        ');
        $stmt->execute([
            $this->is_present ? 1 : 0,
            $this->sick_leave_file,
            $this->sick_leave_status,
            $this->sick_leave_comment,
            $this->attendance_id
        ]);
        return $this;
    }

    public function delete() {
        if (!isset($this->attendance_id)) return false;
        $pdo = getPDO();
        $stmt = $pdo->prepare('DELETE FROM attendance WHERE attendance_id = ?');
        return $stmt->execute([$this->attendance_id]);
    }
}
