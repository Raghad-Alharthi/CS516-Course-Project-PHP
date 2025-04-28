<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/StudentClass.php';
require_once __DIR__ . '/../models/ClassModel.php';

class StudentViewModel {
    public $user_id;
    public $first_name;
    public $last_name;
    /** @var ClassModel[] */
    public $assigned_classes;

    public function __construct($user_id, $first_name, $last_name, $assigned_classes = []) {
        $this->user_id          = $user_id;
        $this->first_name       = $first_name;
        $this->last_name        = $last_name;
        $this->assigned_classes = $assigned_classes;
    }

    /**
     * Build a StudentViewModel for the given student ID.
     */
    public static function forStudent($studentId) {
        $user = User::findById($studentId);
        $scList = StudentClass::allForStudent($studentId);
        $classes = [];
        foreach ($scList as $sc) {
            $classes[] = ClassModel::findById($sc->class_id);
        }
        return new self($user->user_id, $user->first_name, $user->last_name, $classes);
    }
}
