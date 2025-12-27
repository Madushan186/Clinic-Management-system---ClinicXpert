<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('admin');

$patients = $pdo->query("SELECT p.*, u.name, u.email FROM patients p JOIN users u ON p.user_id = u.id")->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="doctors.php">Manage Doctors</a></li>
            <li><a href="patients.php" class="active">Manage Patients</a></li>
            <li><a href="appointments.php">Appointments</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>Manage Patients</h1>

        <div class="card">
            <h3>Patient List</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>DOB</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['email']); ?></td>
                                <td><?php echo htmlspecialchars($p['phone']); ?></td>
                                <td><?php echo htmlspecialchars($p['gender']); ?></td>
                                <td><?php echo formatDate($p['dob']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>