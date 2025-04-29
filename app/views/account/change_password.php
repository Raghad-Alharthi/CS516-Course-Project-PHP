<?php include __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <div class="card shadow p-4" style="border-radius: 12px; background-color: #F1EFEC;">
        <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">Change Password</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success mt-3"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="form-group mb-3">
                <label style="font-family: Funnel Display; color: #123458;">Current Password</label>
                <input type="password" name="old_password" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label style="font-family: Funnel Display; color: #123458;">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label style="font-family: Funnel Display; color: #123458;">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2" style="background-color: #123458; border-color: #123458; font-family: Funnel Display;">
                Update Password
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../shared/footer.php'; ?>
