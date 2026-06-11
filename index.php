<?php
/**
 * Digital School Report Card Management System
 * Main entry point
 */

require_once 'config/config.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['user_role'];
$page_title = "Dashboard - " . APP_NAME;

// Include header
include 'includes/header.php';
?>

<div class="container">
    <header class="dashboard-header">
        <h1>Welcome, <?php echo $_SESSION['full_name']; ?></h1>
        <p>Role: <strong><?php echo ucwords(str_replace('_', ' ', $role)); ?></strong></p>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 20px;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <?php if ($role === 'system_admin'): ?>
            <div class="card">
                <h3>System Management</h3>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/modules/admin/manage_schools.php">Manage Schools</a></li>
                    <li><a href="<?php echo APP_URL; ?>/modules/admin/system_logs.php">System Audit Logs</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($role === 'school_admin' || $role === 'system_admin'): ?>
            <div class="card" style="border: 2px solid var(--primary-color);">
                <h3 style="color: var(--primary-color);">Admin Control Center</h3>
                <p>Access specialized management modules for data integrity, system flow, and reporting.</p>
                <a href="<?php echo APP_URL; ?>/modules/admin/admin_dashboard.php" class="btn btn-primary" style="margin-top: 10px; display: block; text-align: center;">Open Admin Dashboard</a>
            </div>

            <div class="card">
                <h3>School Administration</h3>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/modules/admin/manage_users.php">Manage Staff & Users</a></li>
                    <li><a href="<?php echo APP_URL; ?>/modules/admin/manage_subjects.php">Manage Subjects</a></li>
                    <li><a href="<?php echo APP_URL; ?>/modules/admin/academic_periods.php">Academic Periods</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($role === 'teacher'): ?>
            <div class="card">
                <h3>Academic Management</h3>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/modules/teacher/enter_marks.php">Enter Student Marks</a></li>
                    <li><a href="<?php echo APP_URL; ?>/modules/teacher/view_classes.php">My Classes</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($role === 'bursar'): ?>
            <div class="card">
                <h3>Financial Management</h3>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/modules/bursar/fee_records.php">Manage Fee Records</a></li>
                    <li><a href="<?php echo APP_URL; ?>/modules/bursar/block_reports.php">Block/Unblock Reports</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($role === 'student'): ?>
            <div class="card">
                <h3>Student Portal</h3>
                <ul>
                    <li><a href="<?php echo APP_URL; ?>/modules/student/view_report.php">View My Report Card</a></li>
                    <li><a href="<?php echo APP_URL; ?>/modules/student/history.php">Academic History</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
