<?php
// app/views/admin/manage_students.php
// Variables available: $studentsWithClasses (array of StudentViewModel), $classes (array of ClassModel)
?>
<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">Manage Students</h3>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-info mt-3"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    
    <!-- Existing Students Table -->
    <div class="card shadow p-4" style="border-radius: 12px; background-color: #F1EFEC; margin-bottom:2rem; margin-top:2rem;">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">ID</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Name</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Assigned Classes</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Assign to Class</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentsWithClasses as $student): ?>
                    <tr>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;"><?= htmlspecialchars($student->user_id) ?></td>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;"><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></td>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                            <?php if (!empty($student->assigned_classes)): ?>
                                <ul class="mb-0">
                                    <?php foreach ($student->assigned_classes as $cls): ?>
                                        <li><?= htmlspecialchars($cls->class_name) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <em>No classes</em>
                            <?php endif; ?>
                        </td>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                            <form method="post" action="index.php?route=admin/assignstudenttoclass" class="d-flex">
                                <input type="hidden" name="StudentID" value="<?= htmlspecialchars($student->user_id) ?>">
                                <select style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;" name="ClassID" class="form-select me-2" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $cls): ?>
                                        <option value="<?= htmlspecialchars($cls->class_id) ?>"><?= htmlspecialchars($cls->class_name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                            </form>
                        </td>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                        <form id="form-<?= $student->user_id ?>" method="post" action="index.php?route=admin/deletestudent" style="display: inline;">
                            <input type="hidden" name="studentId" value="<?= $student->user_id ?>">
                            <button type="button" class="btn btn-danger btn-sm delete-btn"
                                    data-class-name="<?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?>"
                                    data-form-id="form-<?= $student->user_id ?>">
                                Delete
                            </button>
                        </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add New Student Form -->
    <div class="card mb-4 shadow p-4" style="border-radius: 12px; background-color: #F1EFEC;">
        <h5 style="font-family: Funnel Display; color: #123458;">Add New Student</h5>
        <form method="post" action="index.php?route=admin/addstudent" class="row g-3 mt-2">
            <div class="col-md-3">
                <input type="text" name="firstName" class="form-control" placeholder="First Name" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="lastName" class="form-control" placeholder="Last Name" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="col-md-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn" style="background-color: #123458; color: #F1EFEC;">Add Student</button>
            </div>
        </form>
    </div>

</div>


<?php require __DIR__ . '/../shared/footer.php'; ?>
