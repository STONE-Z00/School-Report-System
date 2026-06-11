<?php
/**
 * System Backup & Restore
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin']);

$page_title = "System Backup - " . APP_NAME;

// Get DB size info
$db_name = DB_NAME;
$size_query = $pdo->query("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = '$db_name'");
$db_size = round($size_query->fetchColumn(), 2);

include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>System Backup & Restore</h1>
        <p>Ensure your data is safe with regular backups.</p>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <h3>Database Status</h3>
            <p>Database Name: <strong><?php echo DB_NAME; ?></strong></p>
            <p>Current Size: <strong><?php echo $db_size; ?> MB</strong></p>
            <p>Last Backup: <strong>Never</strong></p>
            
            <button class="btn btn-primary" onclick="alert('Backup process initiated. Your download will start shortly.')">Download SQL Backup</button>
        </div>

        <div class="card">
            <h3>Restore Data</h3>
            <p>Upload a previously saved SQL file to restore the system.</p>
            <form>
                <input type="file" class="form-control" style="margin-bottom: 10px;">
                <button type="button" class="btn btn-danger" onclick="confirm('Are you sure? This will overwrite current data!')">Restore from File</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Automatic Backups</h3>
        <p>Status: <span style="color: var(--danger-color);">Disabled</span></p>
        <button class="btn btn-sm">Enable Cloud Backup (Google Drive/Dropbox)</button>
    </div>

    <div style="margin-top: 20px;">
        <a href="admin_dashboard.php" class="btn">Back to Admin Control Center</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
