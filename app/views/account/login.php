<?php
$pageTitle = 'Login';
$error = $error ?? '';
require __DIR__ . '/../shared/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="height: 60%; padding-top: 3%;">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 style="color: #123458; text-align: center; font-family: Funnel Display; font-weight: 700; padding-bottom: 7%;">Login</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?route=account/login">
            <div class="mb-3">
                <label style="font-family: Funnel Display; font-weight: 400; font-size: large; padding-bottom: 2%; color: #123458;">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    style="font-family: Funnel Display; font-weight: 200;"
                    placeholder="Enter your username"
                    required
                >
            </div>
            <div class="mb-3">
                <label style="font-family: Funnel Display; font-weight: 400; font-size: large; padding-bottom: 2%; color: #123458;">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    style="font-family: Funnel Display; font-weight: 200;"
                    placeholder="Enter your password"
                    required
                >
            </div>
            <button
                type="submit"
                class="btn btn-primary w-100"
                style="background-color: #123458; font-family: Funnel Display; color: #F1EFEC; margin-top: 20px;"
            >
                Login
            </button>
        </form>

        <p class="text-center mt-3" style="font-family: Funnel Display; font-weight: 400; color: #123458;">
            Don't have an account? <a href="index.php?route=account/register">Sign up</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
