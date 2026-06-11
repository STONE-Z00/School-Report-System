<?php
/**
 * Parental Communication Portal
 */
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$page_title = "Communication Hub - " . APP_NAME;
include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Communication Hub</h1>
        <p>Keep parents and staff informed via SMS and Email.</p>
    </div>

    <div class="card">
        <h3>Send Bulk Notification</h3>
        <form>
            <div class="form-group">
                <label>Recipient Group</label>
                <select class="form-control">
                    <option>All Parents</option>
                    <option>Primary 5 Parents</option>
                    <option>All Staff</option>
                    <option>Homisdallen Gayaza Only</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message Type</label>
                <div>
                    <input type="checkbox" id="sms" checked> <label for="sms">SMS</label>
                    <input type="checkbox" id="email" checked> <label for="email">Email</label>
                </div>
            </div>
            <div class="form-group">
                <label>Message Content</label>
                <textarea class="form-control" rows="5" placeholder="Type your message here..."></textarea>
            </div>
            <button type="button" class="btn btn-primary" onclick="alert('Notification queued for delivery.')">Send Broadcast</button>
        </form>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Communication History</h3>
        <p>No recent broadcasts found.</p>
    </div>

    <div style="margin-top: 20px;">
        <a href="admin_dashboard.php" class="btn">Back to Admin Control Center</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
