<?php
/**
 * Reporting & Analysis Hub
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$type = $_GET['type'] ?? 'general';
$page_title = "Reporting & Analysis - " . APP_NAME;

include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Reporting & Analysis: <?php echo ucfirst($type); ?></h1>
        <p>Generating insights for school improvement.</p>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <h3>Report Configuration</h3>
            <form>
                <div class="form-group">
                    <label>Select Period</label>
                    <select class="form-control">
                        <option>Term 1 2026</option>
                        <option>Term 3 2025</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Branch</label>
                    <select class="form-control">
                        <option>All Branches</option>
                        <option>Gayaza</option>
                        <option>Kyebando</option>
                        <option>Kamwokya</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="alert('Report Generation Started. Please wait...')">Generate Report</button>
            </form>
        </div>

        <div class="card">
            <h3>Visual Trends (Placeholder)</h3>
            <div style="height: 200px; background: #f4f4f4; display: flex; align-items: center; justify-content: center; border: 2px dashed #ccc;">
                <p>Chart/Graph for <?php echo $type; ?> trends will appear here.</p>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Available Downloads</h3>
        <ul>
            <li><a href="#">Annual Performance Summary (PDF)</a></li>
            <li><a href="#">Branch Comparison Report (Excel)</a></li>
            <li><a href="#">Student Growth Index (CSV)</a></li>
        </ul>
    </div>

    <div style="margin-top: 20px;">
        <a href="admin_dashboard.php" class="btn">Back to Admin Control Center</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
