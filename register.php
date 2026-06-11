<?php
/**
 * User Registration Page
 */

require_once 'config/config.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Fetch schools for the dropdown
try {
    $stmt = $pdo->query("SELECT id, school_name FROM schools ORDER BY school_name ASC");
    $schools = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "System error. Please try again later.";
    $schools = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $full_name = clean_input($_POST['full_name']);
    $email = clean_input($_POST['email']);
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $school_id = $_POST['school_id'];
    $role = $_POST['role'];

    // Student specific fields
    $address = clean_input($_POST['address'] ?? '');
    $guardian_name = clean_input($_POST['guardian_name'] ?? '');
    $guardian_phone = clean_input($_POST['guardian_phone'] ?? '');
    $guardian_work = clean_input($_POST['guardian_work'] ?? '');
    $guardian_profession = clean_input($_POST['guardian_profession'] ?? '');
    $student_age = clean_input($_POST['student_age'] ?? '');
    $dob = clean_input($_POST['dob'] ?? '');
    $current_class = clean_input($_POST['current_class'] ?? '');
    $nationality = clean_input($_POST['nationality'] ?? '');
    $student_status = clean_input($_POST['student_status'] ?? '');
    $lin_number = clean_input($_POST['lin_number'] ?? '');

    // Basic validation
    if (empty($full_name) || empty($email) || empty($username) || empty($password) || empty($confirm_password) || empty($school_id) || empty($role)) {
        $error = "Main account fields are required.";
    } elseif ($role === 'student' && (empty($address) || empty($guardian_name) || empty($guardian_phone) || empty($dob) || empty($current_class) || empty($nationality))) {
        $error = "All student details except optional fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username already taken.";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (school_id, username, password, full_name, email, role, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
                $stmt->execute([$school_id, $username, $hashed_password, $full_name, $email, $role]);
                
                $user_id = $pdo->lastInsertId();

                // If role is student, also create a student record
                if ($role === 'student') {
                    $stmt = $pdo->prepare("INSERT INTO students (user_id, school_id, student_id_number, class_name, address, guardian_name, guardian_phone, guardian_place_of_work, guardian_profession, student_age, date_of_birth, nationality, student_status, lin_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    // Using username as temporary student_id_number
                    $stmt->execute([
                        $user_id, 
                        $school_id, 
                        $username, 
                        $current_class, 
                        $address, 
                        $guardian_name, 
                        $guardian_phone, 
                        $guardian_work, 
                        $guardian_profession, 
                        $student_age, 
                        $dob, 
                        $nationality, 
                        $student_status, 
                        $lin_number
                    ]);
                }
                
                log_action($pdo, $user_id, 'User Self-Registration');
                
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            }
        } catch (PDOException $e) {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .student-only { display: none; background: #f9fafb; padding: 15px; border-radius: 4px; border: 1px solid #e5e7eb; margin-bottom: 15px; }
        .student-only h3 { margin-top: 0; font-size: 1.1rem; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .btn-register { width: 100%; padding: 10px; background: #4a90e2; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .btn-register:hover { background: #357abd; }
        .auth-links { margin-top: 15px; text-align: center; }
    </style>
    <script>
        function toggleStudentFields(role) {
            const fields = document.getElementById('student_fields');
            if (role === 'student') {
                fields.style.display = 'block';
                // Add required attribute to essential student fields
                document.getElementById('address').required = true;
                document.getElementById('guardian_name').required = true;
                document.getElementById('guardian_phone').required = true;
                document.getElementById('dob').required = true;
                document.getElementById('current_class').required = true;
                document.getElementById('nationality').required = true;
            } else {
                fields.style.display = 'none';
                // Remove required attribute
                document.getElementById('address').required = false;
                document.getElementById('guardian_name').required = false;
                document.getElementById('guardian_phone').required = false;
                document.getElementById('dob').required = false;
                document.getElementById('current_class').required = false;
                document.getElementById('nationality').required = false;
            }
        }
    </script>
</head>
<body class="auth-page">
    <div class="register-container">
        <h2 style="text-align: center;"><?php echo APP_NAME; ?> - Registration</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="color: red; background: #fee2e2; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #fecaca;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="color: green; background: #dcfce7; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #bbf7d0;"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="username">Username / ID Number</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="school_id">Select School</label>
                <select id="school_id" name="school_id" required>
                    <option value="">-- Select Your School --</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?php echo $school['id']; ?>"><?php echo $school['school_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="role">Your Role</label>
                <select id="role" name="role" required onchange="toggleStudentFields(this.value)">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="bursar">Bursar</option>
                </select>
            </div>

            <!-- Student Specific Fields -->
            <div id="student_fields" class="student-only" style="display: block;">
                <h3>Student Information</h3>
                
                <div class="form-group">
                    <label for="address">Home Address</label>
                    <textarea id="address" name="address" rows="2"></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="current_class">Current Class</label>
                        <select id="current_class" name="current_class">
                            <option value="">-- Select Class --</option>
                            <option value="Baby Class">Baby Class</option>
                            <option value="Middle Class">Middle Class</option>
                            <option value="Top Class">Top Class</option>
                            <option value="Primary 1">Primary 1</option>
                            <option value="Primary 2">Primary 2</option>
                            <option value="Primary 3">Primary 3</option>
                            <option value="Primary 4">Primary 4</option>
                            <option value="Primary 5">Primary 5</option>
                            <option value="Primary 6">Primary 6</option>
                            <option value="Primary 7">Primary 7</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" name="nationality" value="Ugandan">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob">
                    </div>
                    <div class="form-group">
                        <label for="student_age">Age</label>
                        <input type="number" id="student_age" name="student_age" min="3" max="25">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="student_status">Health/Social Status (Optional)</label>
                        <input type="text" id="student_status" name="student_status" placeholder="e.g. Chronic, Orphan">
                    </div>
                    <div class="form-group">
                        <label for="lin_number">LIN Number (Optional)</label>
                        <input type="text" id="lin_number" name="lin_number">
                    </div>
                </div>

                <h3>Parent/Guardian Information</h3>
                <div class="form-group">
                    <label for="guardian_name">Parent/Guardian Name</label>
                    <input type="text" id="guardian_name" name="guardian_name">
                </div>
                
                <div class="form-group">
                    <label for="guardian_phone">Guardian Contact</label>
                    <input type="text" id="guardian_phone" name="guardian_phone">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="guardian_work">Place of Work</label>
                        <input type="text" id="guardian_work" name="guardian_work">
                    </div>
                    <div class="form-group">
                        <label for="guardian_profession">Profession</label>
                        <input type="text" id="guardian_profession" name="guardian_profession">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-register">Register Account</button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>