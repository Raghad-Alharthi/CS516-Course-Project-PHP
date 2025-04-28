<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <div class="card shadow p-4" style="border-radius: 12px; background-color: #F1EFEC;">
        
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-clipboard-heart" style="font-size: 2rem; color: #123458;"></i>
            <h3 class="ms-3" style="font-family: Funnel Display; color: #123458; font-weight: 800;">
                Sick Leave Status
            </h3>
        </div>

        <p style="font-family: Funnel Display; color: #123458; margin-top: 10px;">
            For Lecture on 
            <strong><?= htmlspecialchars(date('Y-m-d h:i A', strtotime($attendance['lecture_datetime']))) ?></strong>
        </p>

        <p style="font-family: Funnel Display; color: #123458; margin-top: 10px;">
            Status:
            <?php
                $status = $attendance['sick_leave_status'] ?? 'Pending';
                if ($status === 'Accepted') {
                    echo '<span class="badge bg-success">✔ Approved</span>';
                } elseif ($status === 'Rejected') {
                    echo '<span class="badge bg-danger">✖ Rejected</span>';
                } else {
                    echo '<span class="badge bg-warning text-dark">⏳ Pending</span>';
                }
            ?>
        </p>


        <?php if ($status === 'Rejected' && !empty($attendance['sick_leave_comment'])): ?>
            <div class="alert alert-warning mt-3" style="font-family: Funnel Display;">
                <strong>Rejection Reason:</strong> <?= htmlspecialchars($attendance['sick_leave_comment']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($attendance['sick_leave_file'])): ?>
            <p style="font-family: Funnel Display; color: #123458; margin-top: 10px;">
                Uploaded File:
                <a href="/CS516-PHP/public<?= htmlspecialchars($attendance['sick_leave_file']) ?>" 
                   target="_blank" 
                   style="font-weight: bold; text-decoration: underline; color: #123458;">
                    View Document
                </a>
            </p>
        <?php else: ?>
            <p style="font-style: italic; font-family: Funnel Display; color: #123458; margin-top: 10px;">
                No sick leave uploaded.
            </p>
        <?php endif; ?>

        <?php if ($status === 'Rejected'): ?>
            <hr>
            <h5 style="font-family: Funnel Display; color: #123458;">Re-upload Sick Leave Document</h5>

            <form action="index.php?route=student/submitsickleave" method="post" enctype="multipart/form-data">
                <input type="hidden" name="attendanceId" value="<?= htmlspecialchars($attendance['attendance_id']) ?>">

                <div class="mb-3 mt-3">
                    <input type="file" name="sickLeaveFile" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-warning" style="font-family: Funnel Display; font-weight: bold;">
                    Resubmit
                </button>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
