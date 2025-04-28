
<?php // submit_sick_leave.php ?>
<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">Submit Sick Leave</h3>

    <div class="card shadow p-4" style="border-radius: 12px; background-color: #F1EFEC;">
        <form action="index.php?route=student/submitsickleave" method="post" enctype="multipart/form-data">
            <input type="hidden" name="attendanceId" value="<?= htmlspecialchars($attendance['attendance_id']) ?>">

            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458;">Upload Sick Leave Document</label>
                <input class="form-control" type="file" name="sickLeaveFile" required>
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #123458; color: #f8f7f6;">
                Submit
            </button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>

