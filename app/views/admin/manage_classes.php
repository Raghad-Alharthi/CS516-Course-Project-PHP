<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <!-- Page Title -->
    <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">Manage Classes</h3>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-success mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Flash Errors -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger  mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>


        <!-- Classes Table -->
    <div class="card mb-4" style="border-radius: 12px; padding: 20px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); background-color: #F1EFEC;">
        <table class="table table-bordered mt-3" style="background-color: white;">
            <thead style="background-color: #123458; color: #f8f7f6;">
                <tr>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Class Name</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Scheduled Lecture</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Teacher</th>
                    <th style="font-family: Funnel Display; background-color: #123458; color: #f8f7f6;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vm->classes as $classItem): ?>
                    <tr>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                            <?= htmlspecialchars($classItem->class_name) ?>
                        </td>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                            <?php if (!empty($classItem->lectures)): ?>
                                <?php
                                    $firstLecture = new DateTime($classItem->lectures[0]);
                                    echo $firstLecture->format('l - H:i'); 
                                ?>
                            <?php else: ?>
                                <span class="text-muted">No lectures</span>
                            <?php endif; ?>
                        </td>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                            <?php if ($classItem->teacher_id): ?>
                                <?= htmlspecialchars($classItem->teacherName) ?>
                            <?php else: ?>
                                -- Unassigned --
                            <?php endif; ?>
                        </td>
                        <td style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                            <button type="button" class="btn btn-secondary btn-sm me-2" onclick="window.location.href='index.php?route=admin/manageclasses&editId=<?= $classItem->class_id ?>'">Edit</button>
                            <form id="form-<?= $classItem->class_id ?>" method="post" action="index.php?route=admin/deleteclass" style="display: inline;">
                                <input type="hidden" name="classId" value="<?= $classItem->class_id ?>">
                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-class-name="<?= htmlspecialchars($classItem->class_name) ?>" data-form-id="form-<?= $classItem->class_id ?>">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <!-- Add New Class & Schedule Lectures -->
    <div class="card mb-4" style="border-radius: 12px; padding: 20px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); background-color: #F1EFEC;">
        <h4 style="font-family: Funnel Display; color: #123458;">Add New Class & Schedule Lectures</h4>
        <form method="post" action="index.php?route=admin/addclasswithschedule">
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458;">Class Name</label>
                <input type="text" name="className" class="form-control" style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;" required>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458;">Assign Teacher (optional)</label>
                <select name="TeacherID" class="form-select" style="font-family: Funnel Display; background-color: #f8f7f6; color: #123458;">
                    <option value="">-- Unassigned --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t->user_id ?>"><?= htmlspecialchars($t->first_name . ' ' . $t->last_name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458;">Select Lecture Day</label>
                <select name="selectedDay" class="form-select" style="font-family: Funnel Display; background-color: #f8f7f6; color: #123458;" required>
                    <option value="">-- Select a Day --</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-family: Funnel Display; color: #123458;">Select Lecture Time</label>
                <input type="time" name="selectedTime" class="form-control" style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;" required>
            </div>
            <button type="submit" class="btn btn-primary" style="font-family: Funnel Display; background-color: #123458; border-color: #123458;">Add Class & Generate Lectures</button>
        </form>
    </div>

    <!-- Edit Class Section -->
    <?php if ($vm->class_to_edit): ?>
        <div class="card mb-4" style="border-radius: 12px; padding: 20px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); background-color: #F1EFEC;">
            <h4 style="font-family: Funnel Display; color: #123458;">Edit Class</h4>
            <form method="post" action="index.php?route=admin/editclass">
                <input type="hidden" name="ClassID" value="<?= $vm->class_to_edit->class_id ?>">
                <div class="mb-3">
                    <label class="form-label" style="font-family: Funnel Display; color: #123458;">Class Name</label>
                    <input type="text" name="ClassName" value="<?= htmlspecialchars($vm->class_to_edit->class_name) ?>" class="form-control" style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-family: Funnel Display; color: #123458;">Reassign Teacher</label>
                    <select name="TeacherID" class="form-select" style="background-color: #f8f7f6; font-family: Funnel Display; color: #123458;">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?= $t->user_id ?>" <?= $t->user_id === $vm->class_to_edit->teacher_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t->first_name . ' ' . $t->last_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="font-family: Funnel Display; background-color: #123458; border-color: #123458;">Save Changes</button>
            </form>
        </div>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
