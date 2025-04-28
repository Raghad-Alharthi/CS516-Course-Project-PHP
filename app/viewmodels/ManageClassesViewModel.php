<?php
require_once __DIR__ . '/../models/ClassModel.php';

class ManageClassesViewModel {
    /** @var ClassModel[] */
    public $classes;
    /** @var ClassModel|null */
    public $class_to_edit;

    public function __construct($classes = [], $class_to_edit = null) {
        $this->classes        = $classes;
        $this->class_to_edit  = $class_to_edit;
    }

    /**
     * Build the view model, optionally loading one class for editing.
     */
    public static function forAdmin($editClassId = null) {
        $all = ClassModel::all();
        $edit = $editClassId ? ClassModel::findById($editClassId) : null;
        return new self($all, $edit);
    }
}
