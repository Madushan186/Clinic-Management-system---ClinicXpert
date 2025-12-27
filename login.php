<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect(BASE_URL . '/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            session_write_close(); // Ensure session is written before redirect
            if ($user['role'] == 'admin') {
                redirect(BASE_URL . '/admin/index.php');
            } elseif ($user['role'] == 'doctor') {
                redirect(BASE_URL . '/doctor/index.php');
            } else {
                redirect(BASE_URL . '/patient/index.php');
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Welcome Back</h2>
        <p class="mb-4 text-muted">Login to Manage Your Clinic</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <p style="margin-top: 1rem; text-align: center;">
            Don't have an account? <a href="register.php">Sign Up</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>