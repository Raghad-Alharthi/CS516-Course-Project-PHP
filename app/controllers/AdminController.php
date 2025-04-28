<?php
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Lecture.php';
require_once __DIR__ . '/../models/StudentClass.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../viewmodels/StudentViewModel.php';
require_once __DIR__ . '/../viewmodels/ManageClassesViewModel.php';
require_once __DIR__ . '/../viewmodels/AttendanceViewModel.php';

class AdminController
{
    // 🌟 Admin Dashboard
    public function dashboard()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
            header('Location: index.php?route=account/login');
            exit;
        }

        $pdo = getPDO();
        // Classes with teacher name
        $stmt = $pdo->query("
            SELECT c.*, u.first_name AS teacher_first, u.last_name AS teacher_last
            FROM classes c
            LEFT JOIN users u ON c.teacher_id = u.user_id
        ");
        $classes = [];
        while ($row = $stmt->fetch()) {
            $c = new ClassModel($row);
            $c->teacherName = trim($row['teacher_first'] . ' ' . $row['teacher_last']);
            $classes[] = $c;
        }

        // Students & Teachers
        $students = User::allByRole('Student');
        $teachers = User::allByRole('Teacher');

        // Render view
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    // Manage Classes (GET)
    public function manageclasses()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
            header('Location: index.php?route=account/login');
            exit;
        }

        $pdo = getPDO();

        // Fetch all classes with optional teacher names
        $stmt = $pdo->query("SELECT c.*, u.first_name AS teacher_first, u.last_name AS teacher_last
                             FROM classes c
                             LEFT JOIN users u ON c.teacher_id = u.user_id");
        $classes = [];
        while ($row = $stmt->fetch()) {
            $c = new ClassModel($row);
            $c->teacherName = trim(
                ($row['teacher_first'] ?? '') . ' ' .
                ($row['teacher_last']  ?? '')
            );
            $classes[] = $c;
        }

        // Load all teachers for dropdowns
        $teachers = User::allByRole('Teacher');

        // Build ViewModel
        $editId = isset($_GET['editId']) ? (int)$_GET['editId'] : null;
        $vm = new ManageClassesViewModel();
        $vm->classes = $classes;
        $vm->class_to_edit = $editId
            ? ClassModel::findById($editId)
            : null;

        // Pass variables into view
        require __DIR__ . '/../views/admin/manage_classes.php';
    }


    // AddClassWithSchedule (POST)
    public function addclasswithschedule()
    {
        $pdo = getPDO();
        // If no teacher selected, force NULL so FK ON DELETE SET NULL will work
        $teacherId = (isset($_POST['TeacherID']) && $_POST['TeacherID'] !== '')
                     ? (int)$_POST['TeacherID']
                     : null;
        // 1. Create new class
        $class = new ClassModel([
            'class_name' => $_POST['className'],
            'teacher_id' => $teacherId
        ]);
        $class->save();

        // 2. Generate lectures
        $weeksInSemester = 15;
        $targetDay       = $_POST['selectedDay'];     // e.g. "Monday"
        $lectureTime     = $_POST['selectedTime'];    // e.g. "09:00"
        // Find next date for that weekday
        $start = new DateTime();
        while ($start->format('l') !== $targetDay) {
            $start->modify('+1 day');
        }
        for ($i = 0; $i < $weeksInSemester; $i++) {
            $dt = $start->format('Y-m-d') . ' ' . $lectureTime;
            $lec = new Lecture([
                'class_id'        => $class->class_id,
                'lecture_datetime'=> $dt
            ]);
            $lec->save();
            $start->modify('+7 days');
        }

        header('Location: index.php?route=admin/manageclasses');
        exit;
    }

    // DeleteClass (POST)
    public function deleteclass()
    {
        $classId = $_POST['classId'];
        // Remove all related entries
        // 1) StudentClasses
        $pdo = getPDO();
        $pdo->prepare('DELETE FROM student_classes WHERE class_id = ?')
            ->execute([$classId]);
        // 2) Attendance
        $pdo->prepare('DELETE a FROM attendance a
                       JOIN lectures l ON a.lecture_id = l.lecture_id
                       WHERE l.class_id = ?')
            ->execute([$classId]);
        // 3) Lectures
        $pdo->prepare('DELETE FROM lectures WHERE class_id = ?')
            ->execute([$classId]);
        // 4) Class itself
        ClassModel::findById($classId)->delete();

        header('Location: index.php?route=admin/manageclasses');
        exit;
    }

    // EditClass (POST)
    public function editclass()
    {
        $c = ClassModel::findById($_POST['ClassID']);
        $c->teacher_id = $_POST['TeacherID'] ?: null;
        $c->save();
        $_SESSION['message'] = 'Class updated successfully.';
        header('Location: index.php?route=admin/manageclasses');
        exit;
    }

    // Manage Students
    public function managestudents()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
            header('Location: index.php?route=account/login');
            exit;
        }

        // Build array of StudentViewModel
        $studentUsers = User::allByRole('Student');
        $studentsWithClasses = array_map(
            fn($u) => StudentViewModel::forStudent($u->user_id),
            $studentUsers
        );
        $classes = ClassModel::all();

        require __DIR__ . '/../views/admin/manage_students.php';
    }

    // AssignStudentToClass (POST)
    public function assignstudenttoclass()
    {
        $studentId = $_POST['StudentID'];
        $classId   = $_POST['ClassID'];
        // Prevent duplicates
        $exists = (new StudentClass)::allForStudent($studentId);
        $already = array_filter($exists, fn($sc) => $sc->class_id == $classId);
        if (!$already) {
            StudentClass::enroll($studentId, $classId);
        }
        header('Location: index.php?route=admin/managestudents');
        exit;
    }

    // AddStudent (POST)
    public function addstudent()
    {
        $u = new User([
            'first_name'    => $_POST['firstName'],
            'last_name'     => $_POST['lastName'],
            'username'      => $_POST['username'],
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'          => 'Student'
        ]);
        $u->save();
        header('Location: index.php?route=admin/managestudents');
        exit;
    }

    // DeleteStudent (POST)
    public function deletestudent()
    {
        $id = $_POST['studentId'];
        // Remove assignments, then user
        $pdo = getPDO();
        $pdo->prepare('DELETE FROM student_classes WHERE student_id = ?')
            ->execute([$id]);
        User::findById($id)->delete();
        $_SESSION['message'] = 'Student deleted successfully.';
        header('Location: index.php?route=admin/managestudents');
        exit;
    }

    // Manage Teachers
    public function manageteachers()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
            header('Location: index.php?route=account/login');
            exit;
        }
        
        $teachers = User::allByRole('Teacher');
        require __DIR__ . '/../views/admin/manage_teachers.php';
    }

    // AddTeacher (POST)
    public function addteacher()
    {
        $u = new User([
            'first_name'    => $_POST['firstName'],
            'last_name'     => $_POST['lastName'],
            'username'      => $_POST['username'],
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'          => 'Teacher'
        ]);
        $u->save();
        header('Location: index.php?route=admin/manageteachers');
        exit;
    }

    // DeleteTeacher (POST)
    public function deleteteacher()
    {
        $tid = $_POST['TeacherId'];
        $pdo = getPDO();
        // Unassign classes
        $pdo->prepare('UPDATE classes SET teacher_id = NULL WHERE teacher_id = ?')
            ->execute([$tid]);
        // Delete teacher
        User::findById($tid)->delete();
        header('Location: index.php?route=admin/manageteachers');
        exit;
    }

    // AssignTeacherToClass (POST)
    public function assignteachertoclass()
    {
        $cid = $_POST['ClassID'];
        $tid = $_POST['TeacherID'];
        $c   = ClassModel::findById($cid);
        $c->teacher_id = $tid;
        $c->save();
        $_SESSION['message'] = 'Teacher assigned successfully.';
        header('Location: index.php?route=admin/manageclasses');
        exit;
    }

    // View Scheduled Lectures
    public function viewscheduledlectures()
    {
        $classId = $_GET['ClassID'];
        $lectures = Lecture::allByClass($classId);
        // Fetch class details
        $class = ClassModel::findById($classId);
        require __DIR__ . '/../views/admin/view_scheduled_lectures.php';
    }
}
