<?php
// app/views/teacher/manage_class.php
// Variables: $class (ClassModel), $lectures (array of Lecture objects)
require __DIR__ . '/../shared/header.php';
?>

<div class="container my-5">
    <div class="card shadow p-4" style="border-radius: 12px; background-color: #F1EFEC;">
        <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">
            Manage Class: <?= htmlspecialchars($class->class_name) ?>
        </h3>

        <table class="table table-bordered mt-4" style="background-color: white;">
            <thead style="background-color: #123458; color: white;">
                <tr>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Lecture Date</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Time</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Actions</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Excuses</th>
                </tr>
            </thead>
            <tbody style="font-family: Funnel Display; color: #123458;">
                <?php foreach ($lectures as $lecture): ?>
                    <?php 
                        $datetime = new DateTime($lecture->lecture_datetime);
                        $date = $datetime->format('Y-m-d');
                        $time = $datetime->format('h:i A');
                    ?>
                    <tr>
                        <td style="background-color: #f8f7f6;"><?= htmlspecialchars($date) ?></td>
                        <td style="background-color: #f8f7f6;"><?= htmlspecialchars($time) ?></td>
                        <td style="background-color: #f8f7f6;">
                            <a href="index.php?route=teacher/editattendance&lectureId=<?= $lecture->lecture_id ?>"
                               class="btn btn-success btn-sm" style="font-family: Funnel Display;">Edit Attendance</a>
                        </td>
                        <td style="background-color: #f8f7f6;">
                            <a href="index.php?route=teacher/sickleaverequests&lectureId=<?= $lecture->lecture_id ?>"
                               class="btn btn-info btn-sm" style="font-family: Funnel Display;">View Excuses</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
