<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireRole('admin');

// Fetch Stats
$totalDoctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pendingAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'")->fetchColumn();

// Recent Appointments
$recentAppts = $pdo->query("SELECT a.*, p.user_id as p_uid, d.user_id as d_uid, 
                            u1.name as patient_name, u2.name as doctor_name 
                            FROM appointments a 
                            JOIN patients p ON a.patient_id = p.id 
                            JOIN doctors d ON a.doctor_id = d.id 
                            JOIN users u1 ON p.user_id = u1.id 
                            JOIN users u2 ON d.user_id = u2.id 
                            ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                            LIMIT 5")->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-grid">
    <aside class="sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="doctors.php">Manage Doctors</a></li>
            <li><a href="patients.php">Manage Patients</a></li>
            <li><a href="appointments.php">Appointments</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <h1>Dashboard</h1>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalDoctors; ?></div>
                <div class="stat-label">Doctors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalPatients; ?></div>
                <div class="stat-label">Patients</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalAppointments; ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
            <div class="stat-card" style="border-left-color: var(--secondary);">
                <div class="stat-number"><?php echo $pendingAppointments; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="dashboard-grid"
            style="grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; min-height: auto;">
            <div class="card">
                <h3>Appointments by Status</h3>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="card">
                <h3>Overview</h3>
                <canvas id="overviewChart"></canvas>
            </div>
        </div>

        <div class="card">
            <h3>Recent Appointments</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAppts as $appt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appt['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appt['doctor_name']); ?></td>
                                <td>
                                    <?php echo formatDate($appt['appointment_date']); ?>
                                    <small class="text-muted"><?php echo formatTime($appt['appointment_time']); ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($appt['status']); ?>">
                                        <?php echo $appt['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentAppts)): ?>
                            <tr>
                                <td colspan="4">No appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem; text-align: right;">
                <a href="appointments.php" class="btn btn-primary btn-sm">View All</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Status Chart
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        <?php echo $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn(); ?>,
                        <?php echo $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Confirmed'")->fetchColumn(); ?>,
                        <?php echo $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Completed'")->fetchColumn(); ?>,
                        <?php echo $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Cancelled'")->fetchColumn(); ?>
                    ],
                    backgroundColor: ['#fbbf24', '#10b981', '#3b82f6', '#ef4444']
                }]
            },
            options: { responsive: true }
        });

        // Overview Chart (Simple Bar)
        const ctxOverview = document.getElementById('overviewChart').getContext('2d');
        new Chart(ctxOverview, {
            type: 'bar',
            data: {
                labels: ['Doctors', 'Patients', 'Appointments'],
                datasets: [{
                    label: 'Total Count',
                    data: [<?php echo $totalDoctors; ?>, <?php echo $totalPatients; ?>, <?php echo $totalAppointments; ?>],
                    backgroundColor: ['#4f46e5', '#ec4899', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>