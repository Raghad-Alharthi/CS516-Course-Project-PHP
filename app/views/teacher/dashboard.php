<?php
// app/views/teacher/dashboard.php
// Variables: $classes (array of ClassModel)
require __DIR__ . '/../shared/header.php';
?>

<div class="container my-5">
    <!-- Greeting the User -->
    <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">
        Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?> <?= htmlspecialchars($_SESSION['last_name']) ?>!
    </h3>

    <p style="font-family: Funnel Display; color: #123458;">Here are your assigned classes:</p>

    <?php if (!empty($classes)): ?>
        <div class="card mb-4" style="border-radius: 12px; padding: 1rem; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); background-color: #F1EFEC;">
            <div class="list-group mt-3">
                <?php foreach ($classes as $classItem): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #f8f7f6; border-radius: 8px; margin-bottom: 8px;">
                        <span style="font-family: Funnel Display; color: #123458;"><?php echo htmlspecialchars($classItem->class_name); ?></span>
                        <a 
                            href="index.php?route=teacher/manageclass&ClassID=<?php echo $classItem->class_id; ?>"
                            class="btn btn-sm"
                            style="background-color: #123458; color: #F1EFEC; border-color: #123458;"
                        >Manage Class</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="text-danger" style="font-family: Funnel Display;">You are not assigned to any classes yet.</p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
