<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('doctor');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$user_id]);
$doctor_id = $stmt->fetchColumn();

// Fetch unique patients who have had appointments with this doctor
$stmt = $pdo->prepare("
    SELECT DISTINCT p.*, u.name, u.email 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    JOIN users u ON p.user_id = u.id 
    WHERE a.doctor_id = ?
");
$stmt->execute([$doctor_id]);
$patients = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Doctor Panel</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="schedule.php">My Schedule</a></li>
            <li><a href="appointments.php">Appointments</a></li>
            <li><a href="patients.php" class="active">My Patients</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>My Patients</h1>
        
        <div class="card">
             <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>History</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($patients as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><?php echo htmlspecialchars($p['gender']); ?></td>
                            <td><?php echo formatDate($p['dob']); ?></td>
                            <td>
                                <!-- Could link to a full history page -->
                                <button class="btn btn-sm btn-outline" onclick="alert('View History feature coming soon!')">View Medical Records</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($patients)): ?>
                            <tr><td colspan="4">No patients found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
