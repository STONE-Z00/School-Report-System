<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['bursar', 'school_admin']);

$school_id = $_SESSION['school_id'];
$page_title = "Fee Management - " . APP_NAME;
include '../../includes/header.php';

// Fetch fee statuses
$stmt = $pdo->prepare("SELECT f.*, u.full_name, s.student_id_number, ap.term, ap.year 
                      FROM fees_status f
                      JOIN students s ON f.student_id = s.id
                      JOIN users u ON s.user_id = u.id
                      JOIN academic_periods ap ON f.period_id = ap.id
                      WHERE s.school_id = ?");
$stmt->execute([$school_id]);
$fees = $stmt->fetchAll();
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Fee Records & Access Control</h1>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Period</th>
                    <th>Total Payable</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fees as $fee): ?>
                <tr>
                    <td><?php echo $fee['full_name']; ?> (<?php echo $fee['student_id_number']; ?>)</td>
                    <td><?php echo $fee['term'] . ' ' . $fee['year']; ?></td>
                    <td><?php echo number_format($fee['total_payable'], 2); ?></td>
                    <td><?php echo number_format($fee['amount_paid'], 2); ?></td>
                    <td style="color: <?php echo $fee['balance'] > 0 ? 'red' : 'green'; ?>">
                        <?php echo number_format($fee['balance'], 2); ?>
                    </td>
                    <td><span class="badge badge-<?php echo $fee['status']; ?>"><?php echo ucfirst($fee['status']); ?></span></td>
                    <td>
                        <button class="btn btn-sm">Update Payment</button>
                        <?php if ($fee['balance'] > 0): ?>
                            <button class="btn btn-sm btn-danger">Block Report</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
