<?php
$pageTitle = 'Sign Up';
$error = $error ?? '';
require __DIR__ . '/../shared/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 400px; border-radius: 12px;">
        <h3 class="text-center" style="font-family: Funnel Display; color: #123458; font-weight: 700;">Sign Up</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?route=account/register">
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458; font-weight: 400;">First Name</label>
                <input
                    type="text"
                    name="firstName"
                    class="form-control"
                    style="font-family: Funnel Display; font-weight: 200;"
                    placeholder="Enter your first name"
                    required
                >
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458; font-weight: 400;">Last Name</label>
                <input
                    type="text"
                    name="lastName"
                    class="form-control"
                    style="font-family: Funnel Display; font-weight: 200;"
                    placeholder="Enter your last name"
                    required
                >
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458; font-weight: 400;">Username</label>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    style="font-family: Funnel Display; font-weight: 200;"
                    placeholder="Create a username"
                    required
                >
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458; font-weight: 400;">Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    style="font-family: Funnel Display; font-weight: 200;"
                    placeholder="Create a password"
                    required
                >
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458; font-weight: 400;">Role</label>
                <select
                    name="role"
                    class="form-control"
                    style="font-family: Funnel Display; font-weight: 200;"
                    required
                >
                    <option value="Student" style="font-family: Funnel Display; font-weight: 200;">Student</option>
                    <option value="Teacher" style="font-family: Funnel Display; font-weight: 200;">Teacher</option>
                </select>
            </div>
            <button
                type="submit"
                class="btn w-100"
                style="background-color: #123458; color: white; font-family: Funnel Display; border-radius: 6px;"
            >
                Sign Up
            </button>
        </form>

        <p class="text-center mt-3" style="font-family: Funnel Display; color: #123458;">
            Already have an account? <a href="index.php?route=account/login">Login here</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
