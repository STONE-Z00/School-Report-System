<?php
/**
 * System Configuration Viewer
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin']);

$page_title = "System Configuration - " . APP_NAME;
include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>System Configuration</h1>
        <p>Global settings and environment constants.</p>
    </div>

    <div class="card">
        <h3>Application Constants</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Constant</th>
                        <th>Value</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>APP_NAME</code></td>
                        <td><?php echo APP_NAME; ?></td>
                        <td>The official name of the system.</td>
                    </tr>
                    <tr>
                        <td><code>APP_URL</code></td>
                        <td><code><?php echo APP_URL; ?></code></td>
                        <td>Base URL for all links and assets.</td>
                    </tr>
                    <tr>
                        <td><code>DB_NAME</code></td>
                        <td><?php echo DB_NAME; ?></td>
                        <td>Target database name.</td>
                    </tr>
                    <tr>
                        <td><code>DB_HOST</code></td>
                        <td><?php echo DB_HOST; ?></td>
                        <td>Database server location.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="margin-top: 20px; background: #fff3cd; border-left: 5px solid #ffc107;">
        <p><strong>Note:</strong> These settings are defined in <code>config/config.php</code> and <code>config/db.php</code>. To change them, please edit the files directly on the server.</p>
    </div>

    <div style="margin-top: 20px;">
        <a href="admin_dashboard.php" class="btn">Back to Admin Control Center</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
