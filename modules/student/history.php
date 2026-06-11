<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['student']);

$user_id = $_SESSION['user_id'];
$page_title = "Academic History - " . APP_NAME;
include '../../includes/header.php';

// Fetch student profile
$stmt = $pdo->prepare("SELECT s.*, u.full_name FROM students s JOIN users u ON s.user_id = u.id WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "<div class='container'><div class='alert alert-danger'>Student profile not found. Please contact administration.</div></div>";
    include '../../includes/footer.php';
    exit();
}

// Fetch all marks/grades history for this student across all periods
$stmt = $pdo->prepare("
    SELECT m.*, sub.subject_name, ap.term, ap.year 
    FROM marks m 
    JOIN subjects sub ON m.subject_id = sub.id 
    JOIN academic_periods ap ON m.period_id = ap.id 
    WHERE m.student_id = ? 
    ORDER BY ap.year DESC, ap.term DESC, sub.subject_name ASC
");
$stmt->execute([$student['id']]);
$history = $stmt->fetchAll();

// Group history by period for better display
$grouped_history = [];
foreach ($history as $record) {
    $period = $record['term'] . ' ' . $record['year'];
    $grouped_history[$period][] = $record;
}
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Academic History</h1>
        <p>Viewing historical performance for <strong><?php echo $student['full_name']; ?></strong></p>
    </div>

    <?php if (empty($grouped_history)): ?>
        <div class="card" style="text-align: center; padding: 50px;">
            <p style="color: #666; font-size: 1.1rem;">No historical academic records found.</p>
            <a href="<?php echo APP_URL; ?>/modules/student/view_report.php" class="btn btn-primary" style="margin-top: 20px;">View Current Term Report</a>
        </div>
    <?php else: ?>
        <?php foreach ($grouped_history as $period => $records): ?>
            <div class="card" style="margin-bottom: 30px;">
                <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: var(--primary-color);">
                    <?php echo $period; ?>
                </h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Mid Term (40)</th>
                            <th>End Term (60)</th>
                            <th>Total (100)</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <td><?php echo $row['subject_name']; ?></td>
                                <td><?php echo $row['mid_term_mark']; ?></td>
                                <td><?php echo $row['end_term_mark']; ?></td>
                                <td><?php echo $row['total_mark']; ?></td>
                                <td style="font-weight: bold;"><?php echo $row['grade']; ?></td>
                                <td><?php echo $row['teacher_remark']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div style="margin-top: 20px;">
        <a href="<?php echo APP_URL; ?>/index.php" class="btn">Back to Dashboard</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
