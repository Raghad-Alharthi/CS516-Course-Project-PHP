<?php
// app/views/teacher/edit_attendance.php
// Variables passed in: $attendanceList (array of AttendanceViewModel), $lecture (Lecture model)
require __DIR__ . '/../shared/header.php';
?>

<div class="container my-5">
    <div class="card" style="border-radius: 12px; padding: 25px; background-color: #F1EFEC; box-shadow: 0 0 15px rgba(0,0,0,0.05);">
        <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">
            Attendance for Lecture on <?php echo (new DateTime($lecture->lecture_datetime))->format('y-m-d h:i A'); ?>
        </h3>

        <form method="post" action="index.php?route=teacher/saveattendance" class="mt-4">
            <input type="hidden" name="lectureId" value="<?= htmlspecialchars($lecture->lecture_id) ?>" />

            <table class="table table-bordered" style="background-color: #f8f7f6;">
                <thead style="background-color: #123458; color: #f8f7f6; font-family: Funnel Display;">
                    <tr>
                        <th style="font-family: Funnel Display;">Student Name</th>
                        <th style="font-family: Funnel Display;">Present</th>
                    </tr>
                </thead>
                <tbody style="font-family: Funnel Display; color: #123458;">
                    <?php foreach ($attendanceList as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student->full_name) ?></td>
                            <td>
                                <input
                                    type="checkbox"
                                    name="presentStudents[]"
                                    value="<?= htmlspecialchars($student->student_id) ?>"
                                    <?= $student->is_present ? 'checked' : '' ?>
                                />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit"
                    class="btn btn-primary mt-3"
                    style="font-family: Funnel Display; background-color: #123458; border-color: #123458;">
                Save Attendance
            </button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
