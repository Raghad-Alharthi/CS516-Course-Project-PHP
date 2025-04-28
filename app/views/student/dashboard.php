<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">
    Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?> <?= htmlspecialchars($_SESSION['last_name']) ?>!
    </h3>

    <h5 style="font-family: Funnel Display; color: #123458; margin-top: 20px;">
        Your Absence Summary
    </h5>

    <div class="card mb-4 shadow p-4" style="border-radius: 12px; background-color: #F1EFEC;">
        <table class="table table-bordered mt-3" style="background-color: white;">
            <thead style="background-color: #123458; color: white;">
                <tr>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Class</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Absence Percentage</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Missed Lectures</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $c): ?>
                        <tr>
                            <td style="background-color: #f8f7f6; color: #123458; font-family: Funnel Display;">
                                <?= htmlspecialchars($c['class_name']) ?>
                            </td>
                            <td style="background-color: #f8f7f6; color: #123458; font-family: Funnel Display;">
                                <strong><?= htmlspecialchars(number_format($c['absence_percentage'], 2)) ?>%</strong>
                            </td>
                            <td style="background-color: #f8f7f6; color: #123458; font-family: Funnel Display;">
                                <?php if (!empty($c['absences'])): ?>
                                    <ul class="mb-0">
                                    <?php foreach ($c['absences'] as $absence): ?>
                                        <?php
                                            $attendanceId = $absence['attendance_id'];
                                            $lectureDate = htmlspecialchars(date('Y-m-d h:i A', strtotime($absence['lecture_datetime'])));
                                            $sickLeaveStatus = $absence['sick_leave_status'];
                                            $targetUrl = ($sickLeaveStatus === null)
                                                ? "index.php?route=student/submitsickleaveform&attendanceId=$attendanceId"
                                                : "index.php?route=student/tracksickleave&attendanceId=$attendanceId";
                                        ?>
                                        <li>
                                            <a href="<?= $targetUrl ?>" style="text-decoration: none; color: inherit;">
                                                <?= $lectureDate ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>

                                    </ul>
                                <?php else: ?>
                                    <span style="color: green;">No absences</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; font-family: Funnel Display;">No classes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
