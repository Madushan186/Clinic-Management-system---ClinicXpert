<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Assume functions.php is included in the pages that use header, or include it here if not repeatedly included
// include_once __DIR__ . '/functions.php'; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClinicXpert - Clinic Management System</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="container navbar">
            <a href="<?php echo BASE_URL; ?>/" class="logo">
                <i class="fas fa-heartbeat"></i> ClinicXpert
            </a>

            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>

            <nav>
                <ul class="nav-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li><a href="<?php echo BASE_URL; ?>/admin/index.php">Dashboard</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/doctors.php">Doctors</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/patients.php">Patients</a></li>
                        <?php elseif ($_SESSION['role'] == 'doctor'): ?>
                            <li><a href="<?php echo BASE_URL; ?>/doctor/index.php">Dashboard</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/doctor/schedule.php">My Schedule</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>/patient/index.php">Dashboard</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/patient/book.php">Book Appointment</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-outline btn-sm">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary btn-sm">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <!-- Main content starts here -->
        <?php if (function_exists('getFlash'))
            echo getFlash(); ?>