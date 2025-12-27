<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
requireRole('patient');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = ?");
$stmt->execute([$user_id]);
$patient_id = $stmt->fetchColumn();

$history = $pdo->prepare("
    SELECT mh.*, u.name as doctor_name 
    FROM medical_history mh 
    LEFT JOIN doctors d ON mh.doctor_id = d.id 
    LEFT JOIN users u ON d.user_id = u.id 
    WHERE mh.patient_id = ? 
    ORDER BY mh.visit_date DESC
");
$history->execute([$patient_id]);
$records = $history->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Patient Portal</h3>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="book.php">Book Appointment</a></li>
            <li><a href="history.php" class="active">Medical History</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>My Medical History</h1>

        <?php foreach ($records as $rec): ?>
            <div class="card mb-4" style="margin-bottom: 2rem;">
                <div
                    style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                    <h3>Date: <?php echo formatDate($rec['visit_date']); ?></h3>
                    <span class="text-muted">Dr. <?php echo htmlspecialchars($rec['doctor_name']); ?></span>
                </div>

                <div class="mb-4" style="margin-bottom: 1rem;">
                    <strong>Diagnosis:</strong>
                    <p><?php echo nl2br(htmlspecialchars($rec['diagnosis'])); ?></p>
                </div>

                <div style="margin-bottom: 1rem;">
                    <strong>Treatment:</strong>
                    <p><?php echo nl2br(htmlspecialchars($rec['treatment'])); ?></p>
                </div>

                <?php if ($rec['notes']): ?>
                    <div>
                        <strong>Notes:</strong>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($rec['notes'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (empty($records)): ?>
            <div class="alert alert-success">No medical records found. Stay healthy!</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>