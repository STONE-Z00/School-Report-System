<?php
/**
 * Attendance Monitoring for Admin
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$school_id = $_SESSION['school_id'] ?? null;
$page_title = "Attendance Monitoring - " . APP_NAME;

// Fetch attendance stats
$today = date('Y-m-d');
$stats_query = "SELECT status, COUNT(*) as count FROM attendance WHERE date = ? GROUP BY status";
$stats_stmt = $pdo->prepare($stats_query);
$stats_stmt->execute([$today]);
$stats = $stats_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Fetch recent attendance records
$recent_query = "SELECT a.*, u.full_name, s.class_name 
                FROM attendance a 
                JOIN students s ON a.student_id = s.id 
                JOIN users u ON s.user_id = u.id 
                ORDER BY a.created_at DESC LIMIT 50";
$recent_stmt = $pdo->query($recent_query);
$recent_attendance = $recent_stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Attendance Monitoring</h1>
        <p>Overview for <?php echo $today; ?></p>
    </div>

    <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="card" style="text-align: center; border-left: 5px solid var(--success-color);">
            <h4>Present Today</h4>
            <h2 style="color: var(--success-color);"><?php echo $stats['present'] ?? 0; ?></h2>
        </div>
        <div class="card" style="text-align: center; border-left: 5px solid var(--danger-color);">
            <h4>Absent Today</h4>
            <h2 style="color: var(--danger-color);"><?php echo $stats['absent'] ?? 0; ?></h2>
        </div>
        <div class="card" style="text-align: center; border-left: 5px solid var(--warning-color);">
            <h4>Late Today</h4>
            <h2 style="color: var(--warning-color);"><?php echo $stats['late'] ?? 0; ?></h2>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Recent Attendance Records</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_attendance)): ?>
                        <tr><td colspan="5" style="text-align:center;">No attendance records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_attendance as $record): ?>
                        <tr>
                            <td><?php echo $record['full_name']; ?></td>
                            <td><?php echo $record['class_name']; ?></td>
                            <td><?php echo $record['date']; ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo ucfirst($record['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $record['remarks']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="admin_dashboard.php" class="btn">Back to Admin Control Center</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
