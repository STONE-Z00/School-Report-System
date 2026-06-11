<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['student']);

$user_id = $_SESSION['user_id'];
$page_title = "My Report Card - " . APP_NAME;
include '../../includes/header.php';

// Fetch student details, school details and blocking status
$stmt = $pdo->prepare("SELECT s.*, u.full_name, sch.school_badge 
                       FROM students s 
                       JOIN users u ON s.user_id = u.id 
                       JOIN schools sch ON s.school_id = sch.id
                       WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "<div class='container'><div class='alert alert-danger'>Student profile not found. Please contact administration.</div></div>";
    include '../../includes/footer.php';
    exit();
}

$is_blocked = (bool)$student['report_blocked'];
$block_reason = $student['block_reason'] ?? '';

// Check fee balance for blocking
$stmt = $pdo->prepare("SELECT balance FROM fees_status WHERE student_id = ? ORDER BY last_updated DESC LIMIT 1");
$stmt->execute([$student['id']]);
$fee = $stmt->fetch();

if ($fee && $fee['balance'] > 0) {
    $is_blocked = true;
    $block_reason = "Outstanding fee balance of " . number_format($fee['balance'], 2);
}
?>

<div class="container">
    <div class="dashboard-header">
        <h1>My Academic Report</h1>
    </div>

    <?php if ($is_blocked): ?>
        <div class="alert alert-danger" style="padding: 30px; text-align: center; background: #fff5f5; border: 2px solid #feb2b2; border-radius: 8px;">
            <h2 style="color: #c53030;">Report Card Blocked</h2>
            <p style="font-size: 1.2rem; color: #742a2a; margin: 20px 0;">
                <?php echo $block_reason ?: "Please contact the administration office for more details."; ?>
            </p>
            <div style="margin-top: 30px;">
                <p><strong>Guidance:</strong> Visit the Bursar's office or School Admin to resolve this issue.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card report-view">
            <div class="report-header" style="text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
                <div class="school-logo" style="margin-bottom: 10px;">
                    <?php if ($student['school_badge']): ?>
                        <img src="../../uploads/badges/<?php echo $student['school_badge']; ?>" alt="School Badge" style="max-height: 80px;">
                    <?php else: ?>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">[ SCHOOL LOGO ]</div>
                    <?php endif; ?>
                </div>
                <h2>ACADEMIC PROGRESS REPORT</h2>
                <p><strong>Name:</strong> <?php echo $student['full_name']; ?> | <strong>ID:</strong> <?php echo $student['student_id_number']; ?></p>
                <p><strong>Class:</strong> <?php echo $student['class_name']; ?> | <strong>Stream:</strong> <?php echo $student['stream']; ?></p>
            </div>
            
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
                    <!-- Sample data for preview -->
                    <tr>
                        <td>Mathematics</td>
                        <td>35</td>
                        <td>52</td>
                        <td>87</td>
                        <td>A</td>
                        <td>Excellent performance</td>
                    </tr>
                    <tr>
                        <td>English</td>
                        <td>30</td>
                        <td>45</td>
                        <td>75</td>
                        <td>B</td>
                        <td>Good effort, keep it up</td>
                    </tr>
                </tbody>
            </table>

            <div class="digital-signature">
                <div class="sig-box">
                    <div class="sig-placeholder">Verified Digital Signature</div>
                    <div class="sig-label">Class Teacher</div>
                </div>
                <div class="sig-box">
                    <div class="sig-placeholder">Official Stamp Required</div>
                    <div class="sig-label">Head Teacher</div>
                </div>
                <div class="sig-box">
                    <div class="sig-placeholder"><?php echo date('d M Y'); ?></div>
                    <div class="sig-label">Date Issued</div>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: right;" class="no-print">
                <button class="btn btn-primary" onclick="window.print()">Download / Print PDF Report</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
