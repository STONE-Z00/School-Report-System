<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['teacher', 'school_admin']);

$user_id = $_SESSION['user_id'];
$school_id = $_SESSION['school_id'];
$page_title = "My Classes - " . APP_NAME;
include '../../includes/header.php';

// Fetch unique classes and streams in this school
$stmt = $pdo->prepare("SELECT DISTINCT class_name, stream FROM students WHERE school_id = ? ORDER BY class_name ASC, stream ASC");
$stmt->execute([$school_id]);
$classes = $stmt->fetchAll();

// Fetch student count per class
$stmt = $pdo->prepare("SELECT class_name, stream, COUNT(*) as student_count FROM students WHERE school_id = ? GROUP BY class_name, stream");
$stmt->execute([$school_id]);
$counts = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="dashboard-header">
        <h1>My Assigned Classes</h1>
        <p>Overview of classes in your school</p>
    </div>

    <?php if (empty($classes)): ?>
        <div class="card" style="text-align: center; padding: 50px;">
            <p style="color: #666; font-size: 1.1rem;">No classes or students found for this school yet.</p>
            <a href="<?php echo APP_URL; ?>/index.php" class="btn btn-primary" style="margin-top: 20px;">Back to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <?php foreach ($classes as $class): ?>
                <?php 
                    $className = $class['class_name'];
                    $stream = $class['stream'];
                    $studentCount = isset($counts[$className]) ? array_column($counts[$className], 'student_count', 'stream')[$stream] : 0;
                ?>
                <div class="card">
                    <h3><?php echo $className; ?> - <?php echo $stream; ?></h3>
                    <p>Total Students: <strong><?php echo $studentCount; ?></strong></p>
                    <div style="margin-top: 15px;">
                        <a href="<?php echo APP_URL; ?>/modules/teacher/enter_marks.php?class=<?php echo urlencode($className); ?>&stream=<?php echo urlencode($stream); ?>" class="btn btn-sm">Enter Marks</a>
                        <button class="btn btn-sm btn-outline" onclick="alert('Class list feature coming soon!')">View Students</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="margin-top: 30px;">
        <a href="<?php echo APP_URL; ?>/index.php" class="btn">Back to Dashboard</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
