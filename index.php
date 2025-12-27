<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
?>
<!-- Include header manually to control the layout better or use the standard one -->
<?php require_once 'includes/header.php'; ?>

<section class="hero glass"
    style="text-align: center; padding: 4rem 2rem; margin-top: 2rem; border-radius: var(--radius);">
    <h1>Welcome to <span style="color: var(--primary);">ClinicXpert</span></h1>
    <p style="font-size: 1.25rem; color: var(--text-muted); margin-bottom: 2rem;">
        Advanced Clinic Management System for Modern Healthcare.
    </p>

    <div style="display: flex; gap: 1rem; justify-content: center;">
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-primary">Get Started</a>
            <a href="login.php" class="btn btn-outline">Login</a>
        <?php else: ?>
            <p>You are logged in as <strong><?php echo ucfirst($_SESSION['role']); ?></strong>.</p>
            <?php
            $dashboardLink = match ($_SESSION['role']) {
                'admin' => BASE_URL . '/admin/index.php',
                'doctor' => BASE_URL . '/doctor/index.php',
                'patient' => BASE_URL . '/patient/index.php',
                default => BASE_URL . '/'
            };
            ?>
            <a href="<?php echo $dashboardLink; ?>" class="btn btn-primary">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<section class="features-grid"
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 4rem;">
    <div class="card">
        <i class="fas fa-user-md" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;"></i>
        <h3>For Doctors</h3>
        <p>Manage your schedule, view appointments, and access patient records efficiently.</p>
    </div>
    <div class="card">
        <i class="fas fa-procedures" style="font-size: 2.5rem; color: var(--secondary); margin-bottom: 1rem;"></i>
        <h3>For Patients</h3>
        <p>Book appointments, view medical history, and get reminders easily.</p>
    </div>
    <div class="card">
        <i class="fas fa-chart-line" style="font-size: 2.5rem; color: #10b981; margin-bottom: 1rem;"></i>
        <h3>For Admins</h3>
        <p>Comprehensive dashboard to oversee specific clinic operations and statistics.</p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>