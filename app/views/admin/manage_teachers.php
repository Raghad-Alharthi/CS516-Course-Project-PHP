<?php
// app/views/admin/manage_teachers.php
// Variables available: $teachers (array of User objects)
?>
<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">Manage Teachers</h3>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-info mt-3"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    
    <!-- Teachers List -->
    <div class="card shadow" style="border-radius: 12px; padding: 1rem; background-color: #F1EFEC; margin-bottom:2rem; margin-top:2rem;">
        <h5 style="font-family: Funnel Display; color: #123458; font-weight: 700;">Existing Teachers</h5>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">ID</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">First Name</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Last Name</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Username</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;"><?php echo $teacher->user_id; ?></td>
                    <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;"><?php echo htmlspecialchars($teacher->first_name); ?></td>
                    <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;"><?php echo htmlspecialchars($teacher->last_name); ?></td>
                    <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;"><?php echo htmlspecialchars($teacher->username); ?></td>
                    <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                        <form id="form-<?= $teacher->user_id ?>" method="post" action="index.php?route=admin/deleteteacher" style="display: inline;">
                            <input type="hidden" name="TeacherId" value="<?= $teacher->user_id ?>">
                            <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-class-name="<?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?>"
                                    data-form-id="form-<?= $teacher->user_id ?>">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Add Teacher Form -->
    <div class="card mb-4 shadow" style="border-radius: 12px; padding: 1rem; background-color: #F1EFEC;">
        <h5 style="font-family: Funnel Display; color: #123458; font-weight: 700;">Add New Teacher</h5>
        <form method="post" action="index.php?route=admin/addteacher">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" name="firstName" class="form-control" placeholder="First Name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <input type="text" name="lastName" class="form-control" placeholder="Last Name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn w-100" style="background-color: #123458; color: white; font-family: Funnel Display; border-radius: 6px;">Add Teacher</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
