<?php
// app/controllers/StudentController.php

require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Lecture.php';
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../models/StudentClass.php';

class StudentController
{
    // Student Dashboard - Shows Assigned Classes
    public function dashboard()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
            header('Location: index.php?route=account/login');
            exit;
        }

        $pdo = getPDO();
        $studentId = $_SESSION['user_id'];

        $stmt = $pdo->prepare("
            SELECT sc.class_id, c.class_name
            FROM student_classes sc
            JOIN classes c ON sc.class_id = c.class_id
            WHERE sc.student_id = ?
        ");
        $stmt->execute([$studentId]);
        $studentClasses = $stmt->fetchAll();

        $absenceData = [];

        foreach ($studentClasses as $studentClass) {
            $classId = $studentClass['class_id'];
            $className = $studentClass['class_name'];

            $totalLecturesStmt = $pdo->prepare("
                SELECT COUNT(*) FROM lectures WHERE class_id = ?
            ");
            $totalLecturesStmt->execute([$classId]);
            $totalLectures = $totalLecturesStmt->fetchColumn();

            $absentLecturesStmt = $pdo->prepare("
                SELECT a.attendance_id, l.lecture_datetime, a.sick_leave_status
                FROM attendance a
                JOIN lectures l ON a.lecture_id = l.lecture_id
                WHERE a.student_id = ? AND l.class_id = ? AND a.is_present = 0
            ");
            $absentLecturesStmt->execute([$studentId, $classId]);
            $absentLectures = $absentLecturesStmt->fetchAll();

            $absencePercentage = $totalLectures > 0 ? (count($absentLectures) / $totalLectures) * 100 : 0;

            $absenceData[] = [
                'class_name' => $className,
                'absence_percentage' => $absencePercentage,
                'absences' => $absentLectures
            ];
        }

        $classes = $absenceData;
        $user = $_SESSION['user'] ?? null;

        require __DIR__ . '/../views/student/dashboard.php';
    }

    // Handle click on absence
    public function absencedetails()
    {
        $attendanceId = $_GET['attendanceId'] ?? null;
        if (!$attendanceId) {
            http_response_code(400);
            echo "Missing attendanceId";
            exit;
        }

        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE attendance_id = ?");
        $stmt->execute([$attendanceId]);
        $attendance = $stmt->fetch();

        if (!$attendance) {
            http_response_code(404);
            echo "Attendance not found.";
            exit;
        }

        if (empty($attendance['sick_leave_file'])) {
            header('Location: index.php?route=student/submitsickleaveform&attendanceId=' . $attendanceId);
            exit;
        } else {
            header('Location: index.php?route=student/tracksickleave&attendanceId=' . $attendanceId);
            exit;
        }
    }

    // Show Submit Sick Leave form
    public function submitsickleaveform()
    {
        $attendanceId = $_GET['attendanceId'] ?? null;
        if (!$attendanceId) {
            http_response_code(400);
            echo "Missing attendanceId";
            exit;
        }

        $pdo = getPDO();
        $stmt = $pdo->prepare("
            SELECT a.*, l.lecture_datetime 
            FROM attendance a
            JOIN lectures l ON a.lecture_id = l.lecture_id
            WHERE a.attendance_id = ?
        ");
        $stmt->execute([$attendanceId]);
        $attendance = $stmt->fetch();

        if (!$attendance) {
            http_response_code(404);
            echo "Attendance not found.";
            exit;
        }

        require __DIR__ . '/../views/student/submit_sick_leave.php';
    }

    // Submit Sick Leave file (Initial or Resubmit)
    public function submitsickleave()
    {
        $attendanceId = $_POST['attendanceId'] ?? null;

        if (!$attendanceId || !isset($_FILES['sickLeaveFile'])) {
            echo "Invalid request or no file uploaded.";
            exit;
        }

        $sickLeaveFile = $_FILES['sickLeaveFile'];
        $pdo = getPDO();

        // Validate that attendance record exists
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE attendance_id = ?");
        $stmt->execute([$attendanceId]);
        $attendance = $stmt->fetch();

        if (!$attendance) {
            http_response_code(404);
            echo "Attendance not found.";
            exit;
        }

        if ($sickLeaveFile['error'] === UPLOAD_ERR_OK) {
            // Prepare upload directory
            $uploadDir = __DIR__ . '/../../public/sick_leaves/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $originalName = basename($sickLeaveFile['name']);
            $safeName = str_replace(' ', '_', $originalName); // fix spaces in filename
            $targetPath = $uploadDir . $safeName;

            move_uploaded_file($sickLeaveFile['tmp_name'], $targetPath);

            // Update the attendance record
            $stmt = $pdo->prepare("
                UPDATE attendance
                SET sick_leave_file = ?, sick_leave_status = 'Pending', sick_leave_comment = NULL
                WHERE attendance_id = ?
            ");
            $stmt->execute(["/sick_leaves/" . $safeName, $attendanceId]);
        }

        header('Location: index.php?route=student/dashboard');
        exit;
    }

    // View Sick Leave status
    public function tracksickleave()
    {
        $attendanceId = $_GET['attendanceId'] ?? null;
        if (!$attendanceId) {
            http_response_code(400);
            echo "Missing attendanceId";
            exit;
        }

        $pdo = getPDO();
        $stmt = $pdo->prepare("
            SELECT a.attendance_id, a.sick_leave_status, a.sick_leave_file, a.sick_leave_comment, l.lecture_datetime 
            FROM attendance a
            JOIN lectures l ON a.lecture_id = l.lecture_id
            WHERE a.attendance_id = ?
        ");
        $stmt->execute([$attendanceId]);
        $attendance = $stmt->fetch(PDO::FETCH_ASSOC); // <-- IMPORTANT (fetch associative)

        if (!$attendance) {
            http_response_code(404);
            echo "Attendance not found.";
            exit;
        }

        // FIX: Redirect correctly if no document
        if (empty($attendance['sick_leave_file'])) {
            header('Location: index.php?route=student/submitsickleaveform&attendanceId=' . $attendanceId);
            exit;
        }

        require __DIR__ . '/../views/student/track_sick_leave.php';
    }



}
