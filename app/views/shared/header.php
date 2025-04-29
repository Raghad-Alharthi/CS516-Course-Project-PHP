<?php
// app/views/shared/header.php
// This header is included in all views; make sure links are relative to front-controller (index.php?route=...)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '') ?> - Student Management System</title>
    <link rel="stylesheet" href="/css/site.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <nav style="background-color: #F1EFEC; padding: 1%;">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Left Side: Title -->
            <a class="navbar-brand"
               style="font-family: Funnel Display; color: #123458; font-weight: 800; font-size: x-large;"
               href="index.php?route=home/index">
                Student Management Portal
            </a>

            <!-- Right Side: Links -->
            <div class="d-flex align-items-center">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'Admin'): ?>
                        <a style="font-family: Funnel Display; color: #123458; text-decoration: none; margin-left: 20px;"
                           href="index.php?route=admin/dashboard">Admin Panel</a>
                        <a style="font-family: Funnel Display; color: #123458; text-decoration: none; margin-left: 20px;"
                           href="index.php?route=admin/manageteachers">Manage Teachers</a>
                        <a style="font-family: Funnel Display; color: #123458; text-decoration: none; margin-left: 20px;"
                           href="index.php?route=admin/managestudents">Manage Students</a>
                        <a style="font-family: Funnel Display; color: #123458; text-decoration: none; margin-left: 20px;"
                           href="index.php?route=admin/manageclasses">Manage Classes</a>
                    <?php elseif ($_SESSION['role'] === 'Teacher'): ?>
                        <a style="font-family: Funnel Display; color: #123458; text-decoration: none; margin-left: 20px;"
                           href="index.php?route=teacher/dashboard">Teacher Dashboard</a>
                    <?php elseif ($_SESSION['role'] === 'Student'): ?>
                        <a style="font-family: Funnel Display; color: #123458; text-decoration: none; margin-left: 20px;"
                           href="index.php?route=student/dashboard">Student Dashboard</a>
                    <?php endif; ?>

                    <a style="font-family: Funnel Display; color: #123458; text-decoration: none; margin-left: 20px;"
                           href="index.php?route=account/profile">Profile </a>

                    <!-- Common Logout Link -->
                    <a style="font-family: Funnel Display; color:brown; text-decoration: none; margin-left: 20px;"
                       href="index.php?route=account/logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
