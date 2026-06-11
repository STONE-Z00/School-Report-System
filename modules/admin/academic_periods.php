<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['system_admin', 'school_admin']);

$school_id = $_SESSION['school_id'];
$page_title = "Academic Periods - " . APP_NAME;
include '../../includes/header.php';

// Fetch periods
$stmt = $pdo->prepare("SELECT * FROM academic_periods WHERE school_id = ? ORDER BY year DESC, term DESC");
$stmt->execute([$school_id]);
$periods = $stmt->fetchAll();
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Academic Periods</h1>
        <button class="btn btn-primary">Start New Term</button>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Term</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($periods as $period): ?>
                <tr>
                    <td><?php echo $period['year']; ?></td>
                    <td><?php echo $period['term']; ?></td>
                    <td>
                        <span class="badge <?php echo $period['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                            <?php echo $period['is_active'] ? 'Current' : 'Closed'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm">Edit</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
