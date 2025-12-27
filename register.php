<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect(BASE_URL . '/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = cleanInput($_POST['name']);
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $role = cleanInput($_POST['role']);

    // Basic Validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already exists.";
        } else {
            // Hash Password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $pdo->beginTransaction();

                // Insert User
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role]);
                $user_id = $pdo->lastInsertId();

                // Create role specific entry
                if ($role == 'patient') {
                    $dob = cleanInput($_POST['dob']);
                    $gender = cleanInput($_POST['gender']);
                    $phone = cleanInput($_POST['phone']);

                    $stmt = $pdo->prepare("INSERT INTO patients (user_id, dob, gender, phone) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $dob, $gender, $phone]);

                } elseif ($role == 'doctor') {
                    // For doctors, just create a basic entry, they can update profile later
                    // Or keep it simple for now
                    $stmt = $pdo->prepare("INSERT INTO doctors (user_id, specialization, bio) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, 'General', 'Updated bio needed']);
                }

                $pdo->commit();

                setFlash('Account created successfully! Please login.');
                redirect('login.php');

            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Create Account</h2>
        <p class="mb-4 text-muted">Join ClinicXpert today</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" id="roleSelect" onchange="toggleFields()" required>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>

            <div id="patientFields">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
        </form>

        <p style="margin-top: 1rem; text-align: center;">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</div>

<script>
    function toggleFields() {
        const role = document.getElementById('roleSelect').value;
        const patientFields = document.getElementById('patientFields');
        const inputs = patientFields.querySelectorAll('input, select');

        if (role === 'patient') {
            patientFields.style.display = 'block';
            inputs.forEach(input => input.required = true);
        } else {
            patientFields.style.display = 'none';
            inputs.forEach(input => input.required = false);
        }
    }
    // Run on load
    document.addEventListener('DOMContentLoaded', toggleFields);
</script>

<?php require_once 'includes/footer.php'; ?>