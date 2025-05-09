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
            $c->teacherName = trim(($row['teacher_first'] ?? '') . ' ' . ($row['teacher_last'] ?? ''));
            $c->loadLectures(); 
            $classes[] = $c;
        }

        // Load all teachers for dropdowns
        $teachers = User::allByRole('Teacher');

        // Build ViewModel
        $editId = isset($_GET['editId']) ? (int)$_GET['editId'] : null;
        $vm = new ManageClassesViewModel();
        $vm->classes = $classes;
        $vm->class_to_edit = $editId ? ClassModel::findById($editId) : null;

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

        // Validate lecture time and day
        $allowedDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
        $selectedDay = $_POST['selectedDay'];
        $selectedTime = $_POST['selectedTime'];  // format: "HH:MM"

        if (!in_array($selectedDay, $allowedDays)) {
            $_SESSION['error'] = 'Invalid day selected. Only Sunday to Thursday are allowed.';
            header('Location: index.php?route=admin/manageclasses');
            exit;
        }

        $hour = (int)explode(':', $selectedTime)[0];
        if ($hour < 8 || $hour >= 19) {
            $_SESSION['error'] = 'Lecture must be scheduled between 08:00 and 19:00.';
            header('Location: index.php?route=admin/manageclasses');
            exit;
        }

        // Check for overlapping lectures
        $teacherId = (isset($_POST['TeacherID']) && $_POST['TeacherID'] !== '') ? (int)$_POST['TeacherID'] : null;

        if ($teacherId) {
            $pdo = getPDO();
            $checkDate = new DateTime();
            while ($checkDate->format('l') !== $selectedDay) {
                $checkDate->modify('+1 day');
            }
            $lectureStart = new DateTime($checkDate->format('Y-m-d') . ' ' . $selectedTime);
            $lectureEnd = clone $lectureStart;
            $lectureEnd->modify('+2 hour'); // each lecture is 2 hours

            $stmt = $pdo->prepare("
                SELECT l.lecture_datetime
                FROM lectures l
                JOIN classes c ON l.class_id = c.class_id
                WHERE c.teacher_id = ?
                AND TIME(l.lecture_datetime) BETWEEN ? AND ?
                AND DAYNAME(l.lecture_datetime) = ?
            ");
            $stmt->execute([
                $teacherId,
                $lectureStart->modify('-59 minutes')->format('H:i:s'),
                $lectureEnd->modify('+59 minutes')->format('H:i:s'),
                $selectedDay
            ]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Teacher has another lecture that overlaps with the selected time.';
                header('Location: index.php?route=admin/manageclasses');
                exit;
            }
        }


        // Create new class
        $class = new ClassModel([
            'class_name' => $_POST['className'],
            'teacher_id' => $teacherId
        ]);
        $class->save();

        // Generate lectures
        $weeksInSemester = 15;
        $targetDay       = $_POST['selectedDay'];
        $lectureTime     = $_POST['selectedTime']; 
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

        $_SESSION['message'] = 'Class added successfully.';
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

        $_SESSION['message'] = 'Class deleted successfully.';
        header('Location: index.php?route=admin/manageclasses');
        exit;
    }

    // EditClass (POST)
    public function editclass()
    {
        $pdo = getPDO();
        $classId = $_POST['ClassID'];
        $newTeacherId = $_POST['TeacherID'] ?: null;

        // Get current class's scheduled lectures
        $stmt = $pdo->prepare("SELECT lecture_datetime FROM lectures WHERE class_id = ?");
        $stmt->execute([$classId]);
        $lectures = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($newTeacherId && $lectures) {
            foreach ($lectures as $lectureDatetime) {
                $lectureStart = new DateTime($lectureDatetime);
                $lectureEnd = clone $lectureStart;
                $lectureEnd->modify('+2 hours'); // Assuming 2-hour lecture

                // Check if this teacher has overlapping lectures
                $conflictStmt = $pdo->prepare("
                    SELECT l.lecture_datetime
                    FROM lectures l
                    JOIN classes c ON l.class_id = c.class_id
                    WHERE c.teacher_id = ?
                    AND c.class_id != ? -- exclude the current class
                    AND l.lecture_datetime BETWEEN ? AND ?
                ");
                $conflictStmt->execute([
                    $newTeacherId,
                    $classId,
                    $lectureStart->modify('-59 minutes')->format('Y-m-d H:i:s'),
                    $lectureEnd->modify('+59 minutes')->format('Y-m-d H:i:s')
                ]);

                if ($conflictStmt->fetch()) {
                    $_SESSION['error'] = 'Conflict detected: This teacher is already scheduled for another class during one or more lecture times.';
                    header('Location: index.php?route=admin/manageclasses&editId=' . $classId);
                    exit;
                }
            }
        }

        // Save teacher assignment if no conflict
        $c = ClassModel::findById($classId);
        $c->teacher_id = $newTeacherId;
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
            $_SESSION['message'] = 'Student assigned successfully.';
            header('Location: index.php?route=admin/managestudents');
            exit;
        }
        $_SESSION['error'] = 'Student is already assigned.';
        header('Location: index.php?route=admin/managestudents');
        exit;
    }

    // AddStudent (POST)
    public function addstudent()
    {
        $pdo = getPDO();

        // Check for existing username
        $check = $pdo->prepare('SELECT user_id FROM users WHERE username = :username');
        $check->execute(['username' => $_POST['username']]);

        if ($check->fetch()) {
            $_SESSION['error'] = 'Username is already taken. Please choose another one.';
            header('Location: index.php?route=admin/managestudents');
            exit;
        }

        $u = new User([
            'first_name'    => $_POST['firstName'],
            'last_name'     => $_POST['lastName'],
            'username'      => $_POST['username'],
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'          => 'Student'
        ]);
        $u->save();
        $_SESSION['message'] = 'Student added successfully.';
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
        $pdo = getPDO();

        // Check for existing username
        $check = $pdo->prepare('SELECT user_id FROM users WHERE username = :username');
        $check->execute(['username' => $_POST['username']]);

        if ($check->fetch()) {
            $_SESSION['error'] = 'Username is already taken. Please choose another one.';
            header('Location: index.php?route=admin/manageteachers');
            exit;
        }

        // If not taken, proceed to add teacher
        $u = new User([
            'first_name'    => $_POST['firstName'],
            'last_name'     => $_POST['lastName'],
            'username'      => $_POST['username'],
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'          => 'Teacher'
        ]);

        $u->save();
        $_SESSION['message'] = 'Teacher added successfully.';
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
        $_SESSION['message'] = 'Teacher deleted successfully.';
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
