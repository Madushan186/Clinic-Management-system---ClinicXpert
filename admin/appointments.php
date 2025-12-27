<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('admin');

// Handle Status Updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $status = '';

    if ($action == 'confirm')
        $status = 'Confirmed';
    if ($action == 'cancel')
        $status = 'Cancelled';

    if ($status) {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        setFlash("Appointment $status successfully.");
        redirect('appointments.php');
    }
}

$appointments = $pdo->query("
    SELECT a.*, 
           u_p.name as patient_name, 
           u_d.name as doctor_name 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    JOIN users u_p ON p.user_id = u_p.id
    JOIN users u_d ON d.user_id = u_d.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="doctors.php">Manage Doctors</a></li>
            <li><a href="patients.php">Manage Patients</a></li>
            <li><a href="appointments.php" class="active">Appointments</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>All Appointments</h1>

        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($app['doctor_name']); ?></td>
                                <td><?php echo formatDate($app['appointment_date']); ?></td>
                                <td><?php echo formatTime($app['appointment_time']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                        <?php echo $app['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($app['status'] == 'Pending'): ?>
                                        <a href="?action=confirm&id=<?php echo $app['id']; ?>"
                                            class="btn btn-sm btn-primary">Confirm</a>
                                        <a href="?action=cancel&id=<?php echo $app['id']; ?>"
                                            class="btn btn-sm btn-danger">Cancel</a>
                                    <?php endif; ?>
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