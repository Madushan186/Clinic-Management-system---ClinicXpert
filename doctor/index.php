<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('doctor');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$user_id]);
$doctor = $stmt->fetch();
$doctor_id = $doctor['id'];

// Today's Appointments
$today = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT a.*, u.name as patient_name, u.email 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    JOIN users u ON p.user_id = u.id 
    WHERE a.doctor_id = ? AND a.appointment_date = ? 
    ORDER BY a.appointment_time ASC
");
$stmt->execute([$doctor_id, $today]);
$todayAppts = $stmt->fetchAll();

// Pending Requests
$stmt = $pdo->prepare("SELECT count(*) FROM appointments WHERE doctor_id = ? AND status = 'Pending'");
$stmt->execute([$doctor_id]);
$pendingCount = $stmt->fetchColumn();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Doctor Panel</h3>
        <ul>
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="schedule.php">My Schedule</a></li>
            <li><a href="appointments.php">Appointments</a></li>
            <li><a href="patients.php">My Patients</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>Doctor Dashboard</h1>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($todayAppts); ?></div>
                <div class="stat-label">Appointments Today</div>
            </div>
            <div class="stat-card" style="border-left-color: var(--secondary);">
                <div class="stat-number"><?php echo $pendingCount; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>

        <div class="card">
            <h3>Today's Schedule (<?php echo date('M d'); ?>)</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todayAppts as $appt): ?>
                            <tr>
                                <td><?php echo formatTime($appt['appointment_time']); ?></td>
                                <td><?php echo htmlspecialchars($appt['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appt['reason']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($appt['status']); ?>">
                                        <?php echo $appt['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="appointments.php?view=<?php echo $appt['id']; ?>"
                                        class="btn btn-sm btn-outline">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($todayAppts)): ?>
                            <tr>
                                <td colspan="5">No appointments for today.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>