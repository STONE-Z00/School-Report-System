<?php
/**
 * Process Automation Hub
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$page_title = "Process Automation - " . APP_NAME;
include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Process Automation</h1>
        <p>Streamline school operations with smart tools.</p>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-icon" style="font-size: 1.5rem;">📅</div>
            <h3>Automated Timetabling</h3>
            <p>Generate conflict-free schedules for classes and teachers.</p>
            <a href="#" class="btn btn-sm btn-primary">Open Timetable Generator</a>
        </div>

        <div class="card">
            <div class="card-icon" style="font-size: 1.5rem;">🚌</div>
            <h3>Transportation Management</h3>
            <p>Track school van routes, fuel consumption, and student lists.</p>
            <a href="#" class="btn btn-sm btn-primary">Manage Routes</a>
        </div>

        <div class="card">
            <div class="card-icon" style="font-size: 1.5rem;">🎒</div>
            <h3>Inventory & Assets</h3>
            <p>Monitor school property, textbooks, and furniture.</p>
            <a href="#" class="btn btn-sm btn-primary">View Inventory</a>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="admin_dashboard.php" class="btn">Back to Admin Control Center</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
