<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('doctor');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$user_id]);
$doctor_id = $stmt->fetchColumn();

// Handle Status/Completion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['complete_appointment'])) {
        $appt_id = $_POST['appointment_id'];
        $diagnosis = cleanInput($_POST['diagnosis']);
        $treatment = cleanInput($_POST['treatment']);
        $notes = cleanInput($_POST['notes']);
        $patient_id = $_POST['patient_id'];

        try {
            $pdo->beginTransaction();

            // Update appt status
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'Completed' WHERE id = ?");
            $stmt->execute([$appt_id]);

            // Add Medical History
            $stmt = $pdo->prepare("INSERT INTO medical_history (patient_id, doctor_id, visit_date, diagnosis, treatment, notes) VALUES (?, ?, CURDATE(), ?, ?, ?)");
            $stmt->execute([$patient_id, $doctor_id, $diagnosis, $treatment, $notes]);

            $pdo->commit();
            setFlash("Appointment completed and medical record updated.");
            redirect('appointments.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            setFlash("Error: " . $e->getMessage(), 'danger');
        }
    } else if (isset($_POST['update_status'])) {
        $appt_id = $_POST['appointment_id'];
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, $appt_id]);
        setFlash("Status updated.");
        redirect('appointments.php');
    }
}

// Fetch all appointments
$stmt = $pdo->prepare("
    SELECT a.*, u.name as patient_name, p.dob, p.gender 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    JOIN users u ON p.user_id = u.id 
    WHERE a.doctor_id = ? 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$doctor_id]);
$appointments = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Doctor Panel</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="schedule.php">My Schedule</a></li>
            <li><a href="appointments.php" class="active">Appointments</a></li>
            <li><a href="patients.php">My Patients</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>All Appointments</h1>

        <?php
        if (isset($_GET['view'])) {
            $view_id = $_GET['view'];
            $view_appt = null;
            foreach ($appointments as $a) {
                if ($a['id'] == $view_id)
                    $view_appt = $a;
            }

            if ($view_appt):
                ?>
                <div class="card mb-4" style="border: 2px solid var(--primary); margin-bottom: 2rem;">
                    <h3>Manage Appointment #<?php echo $view_appt['id']; ?></h3>
                    <div style="display: flex; gap: 2rem; margin-bottom: 1rem;">
                        <div><strong>Patient:</strong> <?php echo htmlspecialchars($view_appt['patient_name']); ?></div>
                        <div><strong>Date:</strong> <?php echo formatDate($view_appt['appointment_date']); ?>
                            <?php echo formatTime($view_appt['appointment_time']); ?></div>
                        <div><strong>Status:</strong> <?php echo $view_appt['status']; ?></div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <strong>Reason:</strong> <?php echo htmlspecialchars($view_appt['reason']); ?>
                    </div>

                    <?php if ($view_appt['status'] != 'Completed' && $view_appt['status'] != 'Cancelled'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="appointment_id" value="<?php echo $view_appt['id']; ?>">
                            <input type="hidden" name="patient_id" value="<?php echo $view_appt['patient_id']; ?>">
                            <h4>Complete & Add Medical Record</h4>
                            <div class="form-group">
                                <label>Diagnosis</label>
                                <input type="text" name="diagnosis" required>
                            </div>
                            <div class="form-group">
                                <label>Treatment / Medication</label>
                                <textarea name="treatment" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Additional Notes</label>
                                <textarea name="notes"></textarea>
                            </div>
                            <button type="submit" name="complete_appointment" class="btn btn-primary">Complete Appointment</button>
                        </form>

                        <hr style="margin: 1.5rem 0;">

                        <form method="POST" action="" style="display: flex; align-items: center; gap: 1rem;">
                            <input type="hidden" name="appointment_id" value="<?php echo $view_appt['id']; ?>">
                            <label style="margin:0;">Update Status:</label>
                            <select name="status" style="width: auto;">
                                <option value="Confirmed" <?php if ($view_appt['status'] == 'Confirmed')
                                    echo 'selected'; ?>>Confirm
                                </option>
                                <option value="Cancelled">Cancel</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-outline">Update</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-success">This appointment is closed using status:
                            <?php echo $view_appt['status']; ?></div>
                    <?php endif; ?>
                </div>
            <?php
            endif;
        }
        ?>

        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $app): ?>
                            <tr>
                                <td><?php echo formatDate($app['appointment_date']); ?></td>
                                <td><?php echo formatTime($app['appointment_time']); ?></td>
                                <td><?php echo htmlspecialchars($app['patient_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                        <?php echo $app['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?view=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary">Manage</a>
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