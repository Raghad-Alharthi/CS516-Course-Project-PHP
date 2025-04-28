<?php
require_once __DIR__ . '/../config.php';

class StudentClass {
    public $student_class_id;
    public $student_id;
    public $class_id;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function enroll($studentId, $classId) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            INSERT INTO student_classes (student_id, class_id)
            VALUES (?, ?)
        ');
        $stmt->execute([$studentId, $classId]);
        return new self([
            'student_class_id' => $pdo->lastInsertId(),
            'student_id'       => $studentId,
            'class_id'         => $classId
        ]);
    }

    public static function allForStudent($studentId) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM student_classes WHERE student_id = ?');
        $stmt->execute([$studentId]);
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }

    public static function allForClass(int $classId): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'SELECT * FROM student_classes WHERE class_id = ?'
        );
        $stmt->execute([$classId]);
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }
    
    public function delete() {
        if (!isset($this->student_class_id)) return false;
        $pdo = getPDO();
        $stmt = $pdo->prepare('DELETE FROM student_classes WHERE student_class_id = ?');
        return $stmt->execute([$this->student_class_id]);
    }
}
