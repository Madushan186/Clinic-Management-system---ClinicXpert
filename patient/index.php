<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('patient');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient_id = $stmt->fetchColumn();

// Upcoming Appointments
$stmt = $pdo->prepare("
    SELECT a.*, d.specialization, u.name as doctor_name 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    JOIN users u ON d.user_id = u.id 
    WHERE a.patient_id = ? AND a.appointment_date >= CURDATE() 
    ORDER BY a.appointment_date ASC
");
$stmt->execute([$patient_id]);
$upcomingAndRecent = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Patient Portal</h3>
        <ul>
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="book.php">Book Appointment</a></li>
            <li><a href="history.php">Medical History</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>My Health Dashboard</h1>

        <div class="card mb-4" style="text-align: center; margin-bottom: 2rem;">
            <h2>Need a Doctor?</h2>
            <p>Find the best specialists and book your appointment now.</p>
            <a href="book.php" class="btn btn-primary btn-lg">Book Appointment Name</a>
        </div>

        <div class="card">
            <h3>Upcoming Appointments</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingAndRecent as $appt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appt['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($appt['specialization']); ?></td>
                                <td>
                                    <?php echo formatDate($appt['appointment_date']); ?>
                                    <br>
                                    <?php echo formatTime($appt['appointment_time']); ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($appt['status']); ?>">
                                        <?php echo $appt['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($upcomingAndRecent)): ?>
                            <tr>
                                <td colspan="4">No upcoming appointments.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>