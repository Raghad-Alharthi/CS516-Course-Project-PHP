<?php
require_once __DIR__ . '/../config.php';

class ClassModel {
    public $class_id;
    public $class_name;
    public $teacher_id;
    public $teacherName;
    public $lectures = [];

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function findById($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM classes WHERE class_id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public static function all() {
        $pdo = getPDO();
        $stmt = $pdo->query('SELECT * FROM classes');
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }

    public static function allByTeacher(int $teacherId): array {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'SELECT * FROM classes WHERE teacher_id = ?'
        );
        $stmt->execute([$teacherId]);
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }

    public function loadLectures() {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT lecture_datetime FROM lectures WHERE class_id = ? ORDER BY lecture_datetime ASC");
        $stmt->execute([$this->class_id]);
        $this->lectures = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function save() {
        $pdo = getPDO();
        if (isset($this->class_id)) {
            $stmt = $pdo->prepare('
                UPDATE classes SET class_name = ?, teacher_id = ?
                WHERE class_id = ?
            ');
            $stmt->execute([
                $this->class_name,
                $this->teacher_id,
                $this->class_id
            ]);
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO classes (class_name, teacher_id)
                VALUES (?, ?)
            ');
            $stmt->execute([
                $this->class_name,
                $this->teacher_id
            ]);
            $this->class_id = $pdo->lastInsertId();
        }
        return $this;
    }

    public function delete() {
        if (!isset($this->class_id)) return false;
        $pdo = getPDO();
        $stmt = $pdo->prepare('DELETE FROM classes WHERE class_id = ?');
        return $stmt->execute([$this->class_id]);
    }
}
