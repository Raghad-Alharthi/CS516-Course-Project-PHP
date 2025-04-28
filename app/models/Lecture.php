<?php
require_once __DIR__ . '/../config.php';

class Lecture {
    public $lecture_id;
    public $class_id;
    public $lecture_datetime;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function findById($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM lectures WHERE lecture_id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public static function allByClass($classId) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM lectures WHERE class_id = ?');
        $stmt->execute([$classId]);
        return array_map(fn($r) => new self($r), $stmt->fetchAll());
    }

    public static function countByClass(int $classId): int {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM lectures WHERE class_id = ?');
        $stmt->execute([$classId]);
        return (int)$stmt->fetchColumn();
    }
    
    public function save() {
        $pdo = getPDO();
        if (isset($this->lecture_id)) {
            $stmt = $pdo->prepare('
                UPDATE lectures SET class_id = ?, lecture_datetime = ?
                WHERE lecture_id = ?
            ');
            $stmt->execute([
                $this->class_id,
                $this->lecture_datetime,
                $this->lecture_id
            ]);
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO lectures (class_id, lecture_datetime)
                VALUES (?, ?)
            ');
            $stmt->execute([
                $this->class_id,
                $this->lecture_datetime
            ]);
            $this->lecture_id = $pdo->lastInsertId();
        }
        return $this;
    }

    public function delete() {
        if (!isset($this->lecture_id)) return false;
        $pdo = getPDO();
        $stmt = $pdo->prepare('DELETE FROM lectures WHERE lecture_id = ?');
        return $stmt->execute([$this->lecture_id]);
    }
}
