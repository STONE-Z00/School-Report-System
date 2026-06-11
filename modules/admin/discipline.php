<?php
/**
 * Discipline Records Monitoring
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$page_title = "Discipline Monitoring - " . APP_NAME;

// Fetch discipline records
$records = $pdo->query("SELECT d.*, u.full_name, s.class_name 
                       FROM discipline_records d 
                       JOIN students s ON d.student_id = s.id 
                       JOIN users u ON s.user_id = u.id 
                       ORDER BY d.created_at DESC LIMIT 50")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Discipline & Behavior Records</h1>
        <p>Monitoring student conduct across all branches.</p>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Infraction</th>
                        <th>Action Taken</th>
                        <th>Blocks Report?</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="6" style="text-align:center;">No discipline records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo $record['full_name']; ?></td>
                            <td><?php echo $record['class_name']; ?></td>
                            <td><?php echo $record['infraction']; ?></td>
                            <td><?php echo $record['action_taken']; ?></td>
                            <td>
                                <?php if ($record['is_blocking_report']): ?>
                                    <span class="badge badge-danger">YES</span>
                                <?php else: ?>
                                    <span class="badge badge-success">NO</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($record['created_at'])); ?></td>
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
