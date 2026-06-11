<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$page_title = "System Audit Logs - " . APP_NAME;

// Filtering logic
$where = [];
$params = [];

if ($_SESSION['user_role'] === 'school_admin') {
    // School admins only see logs related to their school's users
    $where[] = "(u.school_id = ? OR a.user_id = 0)";
    $params[] = $_SESSION['school_id'];
}

$filter_action = $_GET['action'] ?? '';
if ($filter_action) {
    $where[] = "a.action LIKE ?";
    $params[] = "%$filter_action%";
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Fetch recent logs
$query = "SELECT a.*, u.username, u.full_name 
          FROM audit_logs a 
          LEFT JOIN users u ON a.user_id = u.id 
          $where_sql 
          ORDER BY a.created_at DESC 
          LIMIT 200";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>System Audit Logs</h1>
        <p>Monitor all user actions and system changes.</p>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 10px; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Filter by Action</label>
                <input type="text" name="action" class="form-control" value="<?php echo clean_input($filter_action); ?>" placeholder="e.g. Login">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="system_logs.php" class="btn">Clear</a>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>ID</th>
                        <th>IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="7" style="text-align:center;">No logs found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><small><?php echo date('d M Y, H:i:s', strtotime($log['created_at'])); ?></small></td>
                            <td>
                                <strong><?php echo $log['username'] ?: 'Guest'; ?></strong><br>
                                <small><?php echo $log['full_name']; ?></small>
                            </td>
                            <td><span class="badge badge-info"><?php echo $log['action']; ?></span></td>
                            <td><code><?php echo $log['table_name'] ?: '-'; ?></code></td>
                            <td><?php echo $log['record_id'] ?: '-'; ?></td>
                            <td><small><?php echo $log['ip_address']; ?></small></td>
                            <td>
                                <?php if ($log['new_values']): ?>
                                    <button class="btn btn-sm" onclick="alert('Details: <?php echo addslashes($log['new_values']); ?>')">View</button>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
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
