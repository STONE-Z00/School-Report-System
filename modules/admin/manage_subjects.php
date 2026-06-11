<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$school_id = $_SESSION['school_id'];
$page_title = "Manage Subjects - " . APP_NAME;
include '../../includes/header.php';

// Fetch subjects
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE school_id = ?");
$stmt->execute([$school_id]);
$subjects = $stmt->fetchAll();
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Subject Management</h1>
        <button class="btn btn-primary" onclick="alert('Add Subject Logic Here')">Add New Subject</button>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?php echo $subject['subject_code']; ?></td>
                    <td><?php echo $subject['subject_name']; ?></td>
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
