<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <!-- Page Title -->
    <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">
        Admin Dashboard
    </h3>
    <p style="font-family: Funnel Display; color: #123458;">
        Welcome to the Admin Panel. Use the navigation buttons below to manage the system.
    </p>

    <!-- Classes Section -->
    <div class="card mb-4" style="border-radius: 12px; padding: 20px; 
         box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); background-color: #F1EFEC;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <a style="font-family: Funnel Display; color: #123458; text-decoration: none;">
                    Classes
                </a>
            </h4>
            <a href="index.php?route=admin/manageclasses"
               class="btn"
               style="background-color: #123458; border-color: #123458; color: #F1EFEC;">
                Manage
            </a>
        </div>
        <ul class="list-group">
            <?php foreach ($classes as $classItem): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    style="background-color: #f8f7f6;">
                    <span style="font-family: Funnel Display; color: #123458;">
                        <?= htmlspecialchars($classItem->class_name) ?> —
                        Taught by <?= htmlspecialchars($classItem->teacherName ?: 'Unassigned') ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Teachers Section -->
    <div class="card mb-4" style="border-radius: 12px; padding: 20px; 
         box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); background-color: #F1EFEC;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <a style="font-family: Funnel Display; color: #123458; text-decoration: none;">
                    Teachers
                </a>
            </h4>
            <a href="index.php?route=admin/manageteachers"
               class="btn"
               style="background-color: #123458; border-color: #123458; color: #F1EFEC;">
                Manage
            </a>
        </div>
        <ul class="list-group">
            <?php foreach ($teachers as $t): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    style="background-color: #f8f7f6;">
                    <span style="font-family: Funnel Display; color: #123458;">
                        <?= htmlspecialchars($t->first_name . ' ' . $t->last_name) ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Students Section -->
    <div class="card mb-4" style="border-radius: 12px; padding: 20px; 
         box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); background-color: #F1EFEC;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <a style="font-family: Funnel Display; color: #123458; text-decoration: none;">
                    Students
                </a>
            </h4>
            <a href="index.php?route=admin/managestudents"
               class="btn"
               style="background-color: #123458; border-color: #123458; color: #F1EFEC;">
                Manage
            </a>
        </div>
        <ul class="list-group">
            <?php foreach ($students as $s): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    style="background-color: #f8f7f6;">
                    <span style="font-family: Funnel Display; color: #123458;">
                        <?= htmlspecialchars($s->first_name . ' ' . $s->last_name) ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
