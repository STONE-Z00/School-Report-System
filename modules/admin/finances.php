<?php
/**
 * Financial Monitoring for Admin
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$page_title = "Financial Monitoring - " . APP_NAME;

// Basic stats from fees_status
$fees_stats = $pdo->query("SELECT 
    SUM(total_payable) as total_expected, 
    SUM(amount_paid) as total_collected, 
    SUM(balance) as total_outstanding 
    FROM fees_status")->fetch();

// Recent transactions or status changes
$recent_fees = $pdo->query("SELECT f.*, u.full_name, s.class_name 
                           FROM fees_status f 
                           JOIN students s ON f.student_id = s.id 
                           JOIN users u ON s.user_id = u.id 
                           ORDER BY f.last_updated DESC LIMIT 20")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Financial Monitoring</h1>
        <p>School Fees & Revenue Overview</p>
    </div>

    <div class="dashboard-grid">
        <div class="card" style="border-top: 4px solid var(--primary-color);">
            <h4>Total Expected</h4>
            <h2>UGX <?php echo number_format($fees_stats['total_expected'] ?? 0); ?></h2>
        </div>
        <div class="card" style="border-top: 4px solid var(--success-color);">
            <h4>Total Collected</h4>
            <h2>UGX <?php echo number_format($fees_stats['total_collected'] ?? 0); ?></h2>
        </div>
        <div class="card" style="border-top: 4px solid var(--danger-color);">
            <h4>Total Outstanding</h4>
            <h2>UGX <?php echo number_format($fees_stats['total_outstanding'] ?? 0); ?></h2>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Fee Status Summary</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Payable</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_fees as $fee): ?>
                    <tr>
                        <td><?php echo $fee['full_name']; ?></td>
                        <td><?php echo $fee['class_name']; ?></td>
                        <td><?php echo number_format($fee['total_payable']); ?></td>
                        <td><?php echo number_format($fee['amount_paid']); ?></td>
                        <td style="color: <?php echo $fee['balance'] > 0 ? 'var(--danger-color)' : 'var(--success-color)'; ?>">
                            <?php echo number_format($fee['balance']); ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $fee['status'] === 'paid' ? 'success' : ($fee['status'] === 'partial' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo ucfirst($fee['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="admin_dashboard.php" class="btn">Back to Admin Control Center</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
