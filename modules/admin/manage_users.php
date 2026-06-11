<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$school_id = $_SESSION['school_id'];
$page_title = "Manage Users - " . APP_NAME;

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    $action = $_GET['action'];
    $new_status = ($action === 'enable') ? 'active' : 'inactive';
    
    // Check permission to modify this user
    $check = $pdo->prepare("SELECT role, school_id, username FROM users WHERE id = ?");
    $check->execute([$target_id]);
    $target_user = $check->fetch();

    if ($target_user) {
        $can_edit = false;
        if ($_SESSION['user_role'] === 'system_admin') $can_edit = true;
        elseif ($_SESSION['user_role'] === 'school_admin' && $target_user['school_id'] == $school_id && $target_user['role'] !== 'system_admin') $can_edit = true;

        if ($can_edit) {
            $update = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $update->execute([$new_status, $target_id]);
            
            // LOG THE ACTION
            log_action($pdo, $_SESSION['user_id'], "User Status Changed to $new_status", "users", $target_id, null, "Username: " . $target_user['username']);
            
            $_SESSION['success'] = "User status updated successfully.";
        } else {
            $_SESSION['error'] = "Unauthorized to edit this user.";
        }
    }
    header("Location: manage_users.php");
    exit();
}

include '../../includes/header.php';

// Fetch users based on role
$query = "SELECT * FROM users";
$params = [];

if ($_SESSION['user_role'] === 'school_admin') {
    $query .= " WHERE school_id = ?";
    $params[] = $school_id;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="container">
    <div class="dashboard-header">
        <h1>User Management</h1>
        <a href="<?php echo APP_URL; ?>/register.php" class="btn btn-primary">Add New User</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['full_name']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo ucwords(str_replace('_', ' ', $user['role'])); ?></td>
                    <td>
                        <span class="badge <?php echo $user['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $user['status']; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm">Edit</button>
                        <?php if ($user['status'] === 'active'): ?>
                            <a href="manage_users.php?action=disable&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to disable this user?')">Disable</a>
                        <?php else: ?>
                            <a href="manage_users.php?action=enable&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success">Enable</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
