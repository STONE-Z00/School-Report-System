<?php
/**
 * Security Protocols & IP Blocking
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin']);

$page_title = "Security & Access Control - " . APP_NAME;

// Fetch failed login attempts
$failed = $pdo->query("SELECT * FROM login_attempts ORDER BY last_attempt DESC")->fetchAll();

include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Security & Access Control</h1>
        <p>Monitor failed logins and manage IP restrictions.</p>
    </div>

    <div class="card">
        <h3>Login Security Protocols</h3>
        <p>Current Policy: <strong>Lock account after 5 failed attempts.</strong></p>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Attempts</th>
                        <th>Last Attempt</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($failed)): ?>
                        <tr><td colspan="4" style="text-align:center;">No suspicious activity recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($failed as $f): ?>
                        <tr>
                            <td><?php echo $f['ip_address']; ?></td>
                            <td><?php echo $f['attempts']; ?></td>
                            <td><?php echo $f['last_attempt']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger">Block IP</button>
                            </td>
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
