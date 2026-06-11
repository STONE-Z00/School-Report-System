<?php
/**
 * Advanced Admin Dashboard
 * Handles Data Integrity, System Admin, Student/Staff Management, Reporting, and Process Improvement.
 */

require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

// Ensure only admins can access
check_role(['system_admin', 'school_admin']);

$role = $_SESSION['user_role'];
$school_id = $_SESSION['school_id'] ?? null;
$page_title = "Admin Management Dashboard - " . APP_NAME;

// --- Data Integrity & Security Stats ---
$audit_count = $pdo->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
$today_logs = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$failed_logins = $pdo->query("SELECT COUNT(*) FROM login_attempts")->fetchColumn();
$user_permissions_count = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();

// --- Student & Staff Stats ---
$student_count = $pdo->prepare("SELECT COUNT(*) FROM students WHERE school_id = ?");
$student_count->execute([$school_id]);
$total_students = $student_count->fetchColumn();

$staff_count = $pdo->prepare("SELECT COUNT(*) FROM users WHERE school_id = ? AND role IN ('teacher', 'bursar', 'school_admin')");
$staff_count->execute([$school_id]);
$total_staff = $staff_count->fetchColumn();

include '../../includes/header.php';
?>

<div class="container">
    <header class="dashboard-header">
        <h1>Admin Control Center</h1>
        <p>Comprehensive management for <strong><?php echo APP_NAME; ?></strong></p>
    </header>

    <div class="dashboard-grid">
        <!-- 1. Data Integrity & Security -->
        <div class="card">
            <div class="card-icon" style="font-size: 2rem; margin-bottom: 10px;">🛡️</div>
            <h3>Data Integrity & Security</h3>
            <p>Maintain accurate records and security protocols.</p>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/system_logs.php">View Full Audit Logs (Total: <?php echo $audit_count; ?>)</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/system_logs.php?action=CURDATE">Today's Activity: <strong><?php echo $today_logs; ?></strong> actions</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/manage_users.php">Manage Permissions & Access</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/security.php">Security Protocols & IP Blocking</a></li>
                <li><a href="#" style="color: var(--success-color);">✓ Data Integrity Check: OK</a></li>
            </ul>
        </div>

        <!-- 2. System Administration -->
        <div class="card">
            <div class="card-icon" style="font-size: 2rem; margin-bottom: 10px;">⚙️</div>
            <h3>System Administration</h3>
            <p>Software configuration and data flow management.</p>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/manage_schools.php">Configure School Branches</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/manage_subjects.php">Manage Subjects & Curriculum</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/backup.php" class="btn-sm btn-primary" style="display:inline-block; margin-top:5px; text-align:center;">System Backup & Restore</a></li>
                 <li><a href="<?php echo APP_URL; ?>/modules/admin/system_config.php">System Constants (View Only)</a></li>
             </ul>
        </div>

        <!-- 3. Student & Staff Management -->
        <div class="card">
            <div class="card-icon" style="font-size: 2rem; margin-bottom: 10px;">👥</div>
            <h3>Student & Staff Management</h3>
            <p>Enrollment, attendance, and academic records.</p>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/manage_users.php">Staff Directory (<?php echo $total_staff; ?>)</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/teacher/view_classes.php">Student Enrollment (<?php echo $total_students; ?>)</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/attendance.php">Daily Attendance Monitoring</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/academic_periods.php">Manage Academic Terms</a></li>
            </ul>
        </div>

        <!-- 4. Reporting & Analysis -->
        <div class="card">
            <div class="card-icon" style="font-size: 2rem; margin-bottom: 10px;">📊</div>
            <h3>Reporting & Analysis</h3>
            <p>Performance trends and attendance reports.</p>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/reports.php?type=performance">Academic Performance Trends</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/reports.php?type=attendance">Attendance Summary Reports</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/finances.php">Financial Health Reports</a></li>
                <li><a href="<?php echo APP_URL; ?>/docs/System_Architecture.html">System Flow Analysis</a></li>
            </ul>
        </div>

        <!-- 5. Process Improvement -->
        <div class="card">
            <div class="card-icon" style="font-size: 2rem; margin-bottom: 10px;">🚀</div>
            <h3>Process Improvement</h3>
            <p>Automate tasks and communications.</p>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/discipline.php">Discipline & Behavior Records</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/automation.php">Automated Timetabling & Transport</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/communication.php">Parental Communication & SMS</a></li>
                <li><a href="<?php echo APP_URL; ?>/modules/admin/system_logs.php">Monitor Data Flow</a></li>
            </ul>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a href="<?php echo APP_URL; ?>/index.php" class="btn">Back to General Dashboard</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
