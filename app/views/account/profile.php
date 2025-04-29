<?php include __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <div class="card shadow p-4" style="border-radius: 12px; background-color: #F1EFEC;">
        <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">My Profile</h3>

        <p style="font-family: Funnel Display; color: #123458;">
            <strong>First Name:</strong> <?= htmlspecialchars($_SESSION['first_name']) ?>
        </p>
        <p style="font-family: Funnel Display; color: #123458;">
            <strong>Last Name:</strong> <?= htmlspecialchars($_SESSION['last_name']) ?>
        </p>
        <p style="font-family: Funnel Display; color: #123458;">
            <strong>Username:</strong> <?= htmlspecialchars($_SESSION['username']) ?>
        </p>
        <p style="font-family: Funnel Display; color: #123458;">
            <strong>Role:</strong> <?= htmlspecialchars($_SESSION['role']) ?>
        </p>

        <a href="index.php?route=account/changePassword" class="btn btn-warning mt-3" style="background-color: #123458; border-color: #123458; font-family: Funnel Display; color:#F1EFEC">Change Password</a>
    </div>
</div>

<?php include __DIR__ . '/../shared/footer.php'; ?>
