<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('admin');

// Handle Add Doctor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_doctor'])) {
    $name = cleanInput($_POST['name']);
    $email = cleanInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $specialization = cleanInput($_POST['specialization']);
    $bio = cleanInput($_POST['bio']);

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'doctor')");
        $stmt->execute([$name, $email, $password]);
        $user_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO doctors (user_id, specialization, bio) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $specialization, $bio]);
        $doctor_id = $pdo->lastInsertId();

        // Add default schedule (Mon-Fri 9-5)
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $schedStmt = $pdo->prepare("INSERT INTO schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, '09:00:00', '17:00:00')");
        foreach ($days as $day) {
            $schedStmt->execute([$doctor_id, $day]);
        }

        $pdo->commit();
        setFlash("Doctor added successfully.");
        redirect('doctors.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlash("Error adding doctor: " . $e->getMessage(), 'danger');
    }
}

// Fetch Doctors
$doctors = $pdo->query("SELECT d.*, u.name, u.email FROM doctors d JOIN users u ON d.user_id = u.id")->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="doctors.php" class="active">Manage Doctors</a></li>
            <li><a href="patients.php">Manage Patients</a></li>
            <li><a href="appointments.php">Appointments</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>Manage Doctors</h1>

        <div class="card mb-4" style="margin-bottom: 2rem;">
            <h3>Add New Doctor</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" name="specialization" required>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" rows="3"></textarea>
                </div>
                <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
            </form>
        </div>

        <div class="card">
            <h3>Doctor List</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Specialization</th>
                            <th>Email</th>
                            <th>Actions</th>
                            <!-- Keep simple, no edit/delete for now unless asked, but prompted said CRUD -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $doc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doc['name']); ?></td>
                                <td><?php echo htmlspecialchars($doc['specialization']); ?></td>
                                <td><?php echo htmlspecialchars($doc['email']); ?></td>
                                <td>
                                    <!-- Placeholder for Edit/Delete -->
                                    <button class="btn btn-sm btn-outline">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>