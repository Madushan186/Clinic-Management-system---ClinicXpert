<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('doctor');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$user_id]);
$doctor_id = $stmt->fetchColumn();

// Update Schedule
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Check if exists
    $check = $pdo->prepare("SELECT id FROM schedules WHERE doctor_id = ? AND day_of_week = ?");
    $check->execute([$doctor_id, $day]);

    if ($check->rowCount() > 0) {
        $update = $pdo->prepare("UPDATE schedules SET start_time = ?, end_time = ? WHERE doctor_id = ? AND day_of_week = ?");
        $update->execute([$start_time, $end_time, $doctor_id, $day]);
    } else {
        $insert = $pdo->prepare("INSERT INTO schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        $insert->execute([$doctor_id, $day, $start_time, $end_time]);
    }
    setFlash("Schedule updated for $day.");
    redirect('schedule.php');
}

// Fetch Schedule
$schedule = $pdo->prepare("SELECT * FROM schedules WHERE doctor_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
$schedule->execute([$doctor_id]);
$slots = $schedule->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE); // Group by day

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Doctor Panel</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="schedule.php" class="active">My Schedule</a></li>
            <li><a href="appointments.php">Appointments</a></li>
            <li><a href="patients.php">My Patients</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>Manage Schedule</h1>

        <div class="card">
            <?php foreach ($days as $day):
                $start = isset($slots[$day]) ? $slots[$day]['start_time'] : '';
                $end = isset($slots[$day]) ? $slots[$day]['end_time'] : '';
                ?>
                <form method="POST" action=""
                    style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                    <input type="hidden" name="day" value="<?php echo $day; ?>">
                    <div style="width: 150px; font-weight: bold; padding-bottom: 0.5rem;"><?php echo $day; ?></div>

                    <div class="form-group" style="width: 100%; margin: 0;">
                        <label style="font-size: 0.8rem;">Start Time</label>
                        <input type="time" name="start_time" value="<?php echo $start; ?>">
                    </div>
                    <div class="form-group" style="width: 100%; margin: 0;">
                        <label style="font-size: 0.8rem;">End Time</label>
                        <input type="time" name="end_time" value="<?php echo $end; ?>">
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>