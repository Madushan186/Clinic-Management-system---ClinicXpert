<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('patient');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient_id = $stmt->fetchColumn();

// Fetch Doctors
$doctors = $pdo->query("SELECT d.*, u.name FROM doctors d JOIN users u ON d.user_id = u.id")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = cleanInput($_POST['reason']);

    // Validations:
    // 1. Check if doctor works on that day
    $dateObj = new DateTime($date);
    $dayOfWeek = $dateObj->format('l');

    $schedStmt = $pdo->prepare("SELECT * FROM schedules WHERE doctor_id = ? AND day_of_week = ?");
    $schedStmt->execute([$doctor_id, $dayOfWeek]);
    $schedule = $schedStmt->fetch();

    if (!$schedule) {
        $error = "Doctor is not available on this day.";
    } elseif ($time < $schedule['start_time'] || $time > $schedule['end_time']) {
        $error = "Doctor is only available between " . formatTime($schedule['start_time']) . " and " . formatTime($schedule['end_time']);
    } else {
        // 2. Check overlap
        $checkStmt = $pdo->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'");
        $checkStmt->execute([$doctor_id, $date, $time]);
        if ($checkStmt->rowCount() > 0) {
            $error = "This slot is already booked. Please choose another time.";
        } else {
            // Book it
            $bookStmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
            $bookStmt->execute([$patient_id, $doctor_id, $date, $time, $reason]);
            $success = "Appointment request sent successfully!";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Patient Portal</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="book.php" class="active">Book Appointment</a></li>
            <li><a href="history.php">Medical History</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>Book New Appointment</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Select Doctor</label>
                    <select name="doctor_id" required>
                        <option value="">-- Choose a Doctor --</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?php echo $doc['id']; ?>">
                                <?php echo htmlspecialchars($doc['name']); ?>
                                (<?php echo htmlspecialchars($doc['specialization']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="time" required>
                    <small class="text-muted">Please check doctor's availability.</small>
                </div>

                <div class="form-group">
                    <label>Reason for Visit</label>
                    <textarea name="reason" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Book Appointment</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>