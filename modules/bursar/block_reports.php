<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['bursar', 'school_admin']);

$school_id = $_SESSION['school_id'];
$page_title = "Block/Unblock Reports - " . APP_NAME;
include '../../includes/header.php';

// Fetch students and their blocking status
$stmt = $pdo->prepare("SELECT s.id, u.full_name, s.student_id_number, s.report_blocked, s.block_reason 
                       FROM students s 
                       JOIN users u ON s.user_id = u.id 
                       WHERE s.school_id = ?");
$stmt->execute([$school_id]);
$students = $stmt->fetchAll();
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Report Card Access Control</h1>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>ID Number</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo $student['full_name']; ?></td>
                    <td><?php echo $student['student_id_number']; ?></td>
                    <td>
                        <span class="badge <?php echo $student['report_blocked'] ? 'badge-danger' : 'badge-success'; ?>">
                            <?php echo $student['report_blocked'] ? 'Blocked' : 'Active'; ?>
                        </span>
                    </td>
                    <td><?php echo $student['block_reason'] ?: '-'; ?></td>
                    <td>
                        <?php if ($student['report_blocked']): ?>
                            <button class="btn btn-sm btn-success">Unblock</button>
                        <?php else: ?>
                            <button class="btn btn-sm btn-danger">Block</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
