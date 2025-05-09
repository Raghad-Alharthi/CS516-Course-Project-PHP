<?php
// app/controllers/TeacherController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../models/StudentClass.php';
require_once __DIR__ . '/../models/Lecture.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../viewmodels/AttendanceViewModel.php';

class TeacherController
{
    // Dashboard: list classes assigned to this teacher
    public function dashboard()
    {
        $username = $_SESSION['username'] ?? null;
        if (!$username) {
            header('Location: index.php?route=account/login'); exit;
        }
        $teacher = User::findByUsername($username);
        if (!$teacher || $teacher->role !== 'Teacher') {
            header('Location: index.php?route=account/login'); exit;
        }

        $classes = ClassModel::allByTeacher($teacher->user_id);
        require __DIR__ . '/../views/teacher/dashboard.php';
    }

    // ManageClass: show past and current lectures for a class
    public function manageclass()
    {
        $classId = $_GET['ClassID'] ?? null;
        if (!$classId) { http_response_code(400); echo 'ClassID missing'; exit; }

        $username = $_SESSION['username'] ?? null;
        $teacher = User::findByUsername($username);
        if (!$teacher) { header('Location: index.php?route=account/login'); exit; }

        $class = ClassModel::findById($classId);
        if (!$class || $class->teacher_id != $teacher->user_id) {
            http_response_code(403); echo 'Unauthorized'; exit;
        }

        $lectures = Lecture::allByClass($classId);
        require __DIR__ . '/../views/teacher/manage_class.php';
    }

    // View/Edit attendance for a lecture
    public function editattendance()
    {
        $lectureId = $_GET['lectureId'] ?? null;
        if (!$lectureId) { http_response_code(400); echo 'lectureId missing'; exit; }

        $lecture = Lecture::findById($lectureId);
        if (!$lecture) { http_response_code(404); echo 'Lecture not found'; exit; }

        // 🛠 NEW: Fetch all students enrolled in the class
        $pdo = getPDO();
        $stmt = $pdo->prepare("
            SELECT u.user_id, u.first_name, u.last_name
            FROM student_classes sc
            JOIN users u ON sc.student_id = u.user_id
            WHERE sc.class_id = ?
        ");
        $stmt->execute([$lecture->class_id]);
        $students = $stmt->fetchAll();

        // 🛠 Fetch existing attendance records
        $existingAttendance = Attendance::findByLecture($lectureId);
        $attendanceByStudentId = [];
        foreach ($existingAttendance as $att) {
            $attendanceByStudentId[$att->student_id] = $att;
        }

        // 🛠 Build $attendanceList for the view
        $attendanceList = [];
        foreach ($students as $s) {
            $attendanceList[] = (object)[
                'student_id' => $s['user_id'],
                'full_name' => $s['first_name'] . ' ' . $s['last_name'],
                'is_present' => isset($attendanceByStudentId[$s['user_id']]) 
                    ? (bool)$attendanceByStudentId[$s['user_id']]->is_present 
                    : true // default to present if no record
            ];
        }

        require __DIR__ . '/../views/teacher/edit_attendance.php';
    }


    // Save attendance POST
    public function saveattendance()
    {
        $lectureId = $_POST['lectureId'] ?? null;
        $presentArr = $_POST['presentStudents'] ?? [];

        if (!$lectureId) {
            http_response_code(400);
            echo 'Lecture ID missing';
            exit;
        }

        $lecture = Lecture::findById($lectureId);
        if (!$lecture) {
            http_response_code(404);
            echo 'Lecture not found';
            exit;
        }

        $pdo = getPDO();
        $pdo->beginTransaction();

        try {
            // Get students who submitted sick leave — we should not touch them
            $stmt = $pdo->prepare("
                SELECT student_id 
                FROM attendance 
                WHERE lecture_id = ? 
                AND (sick_leave_file IS NOT NULL AND sick_leave_status IS NOT NULL)
            ");
            $stmt->execute([$lectureId]);
            $protected = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Get all students in the class
            $classStudents = $pdo->prepare("
                SELECT student_id 
                FROM student_classes 
                WHERE class_id = ?
            ");
            $classStudents->execute([$lecture->class_id]);
            $students = $classStudents->fetchAll(PDO::FETCH_COLUMN);

            // Remove attendance records only for unprotected students
            $stmt = $pdo->prepare("
                DELETE FROM attendance 
                WHERE lecture_id = ? AND student_id = ?
            ");

            foreach ($students as $studentId) {
                if (!in_array($studentId, $protected)) {
                    $stmt->execute([$lectureId, $studentId]);
                }
            }

            // Insert updated absentees, skipping protected
            $insert = $pdo->prepare("
                INSERT INTO attendance (lecture_id, student_id, is_present)
                VALUES (?, ?, 0)
            ");

            foreach ($students as $studentId) {
                if (!in_array($studentId, $presentArr) && !in_array($studentId, $protected)) {
                    $insert->execute([$lectureId, $studentId]);
                }
            }

            $pdo->commit();
            $_SESSION['message'] = 'Attendance updated successfully.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Failed to update attendance.';
        }

        header('Location: index.php?route=teacher/manageclass&ClassID=' . $lecture->class_id);
        exit;
    }



    // Sick leave requests
    public function sickleaverequests()
    {
        $lectureId = $_GET['lectureId'] ?? null;
        if (!$lectureId) {
            http_response_code(400);
            echo "LectureId missing";
            exit;
        }

        $pdo = getPDO();
        $stmt = $pdo->prepare("
            SELECT a.*, l.lecture_datetime
            FROM attendance a
            JOIN lectures l ON a.lecture_id = l.lecture_id
            WHERE a.lecture_id = ?
            AND a.sick_leave_file IS NOT NULL
        ");
        $stmt->execute([$lectureId]);
        $requests = $stmt->fetchAll(PDO::FETCH_OBJ);

        require __DIR__ . '/../views/teacher/sick_leave_requests.php';
    }


    // Approve/Reject sick leave POST
    public function approvesickleave()
    {
        $attendanceId = $_POST['attendanceId'];
        $decision     = $_POST['decision'];
        $comment      = trim($_POST['comment'] ?? '');

        // Require comment if rejecting
        if ($decision === 'Rejected' && empty($comment)) {
            $_SESSION['error'] = 'Rejection comment is required.';
            header('Location: index.php?route=teacher/sickleaverequests&lectureId=' . $attendanceId);
            exit;
        }

        $att = Attendance::findById($attendanceId);
        if ($att) {
            $att->sick_leave_status  = $decision;
            $att->sick_leave_comment = ($decision === 'Rejected') ? $comment : null;
            $att->save();
        }

        $_SESSION['message'] = 'Sick leave request updated.';
        header('Location: index.php?route=teacher/sickleaverequests&lectureId=' . $att->lecture_id);
        exit;
    }

}
