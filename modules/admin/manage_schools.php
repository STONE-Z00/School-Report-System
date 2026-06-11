<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/models/School.php';

check_role(['system_admin']);

$schoolModel = new School($pdo);
$schools = $schoolModel->all();

$page_title = "Manage Schools - " . APP_NAME;
include '../../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Manage Schools</h1>
        <button class="btn btn-primary" onclick="alert('Add School Logic Here')">Add New School</button>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>School Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schools as $school): ?>
                <tr>
                    <td><?php echo $school['id']; ?></td>
                    <td><?php echo $school['school_name']; ?></td>
                    <td><?php echo $school['school_email']; ?></td>
                    <td><?php echo $school['school_phone']; ?></td>
                    <td>
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
