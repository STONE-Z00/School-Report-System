<?php
require_once '../../config/config.php';
require_once '../../config/db.php';
require_once '../../includes/functions.php';

check_role(['teacher', 'school_admin']);

$school_id = $_SESSION['school_id'];
$page_title = "Enter Marks - " . APP_NAME;
include '../../includes/header.php';

// Fetch students for the school
$stmt = $pdo->prepare("SELECT s.id, u.full_name, s.class_name, s.student_id_number 
                      FROM students s 
                      JOIN users u ON s.user_id = u.id 
                      WHERE s.school_id = ?");
$stmt->execute([$school_id]);
$students = $stmt->fetchAll();

// Fetch subjects
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE school_id = ?");
$stmt->execute([$school_id]);
$subjects = $stmt->fetchAll();
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Enter Student Marks</h1>
    </div>

    <div class="card">
        <form action="save_marks.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Select Subject</label>
                    <select name="subject_id" required>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>"><?php echo $subject['subject_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>ID Number</th>
                        <th>Mid Term (40%)</th>
                        <th>End Term (60%)</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['full_name']; ?></td>
                        <td><?php echo $student['student_id_number']; ?></td>
                        <td><input type="number" name="marks[<?php echo $student['id']; ?>][mid]" max="40" step="0.1"></td>
                        <td><input type="number" name="marks[<?php echo $student['id']; ?>][end]" max="60" step="0.1"></td>
                        <td><input type="text" name="marks[<?php echo $student['id']; ?>][remark]"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Save Marks</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
