<?php
/**
 * Test Database Connection and Data
 */

require_once 'config/db.php';

echo "<h2>Database System Setup & Automation</h2>";

try {
    // 1. Test connection
    echo "<p style='color: green;'>✓ Connected to database successfully.</p>";

    // 2. Automate: Ensure only Homisdallen branches exist
    $allowed_branch_names = [
        'Homisdallen Primary School - Gayaza',
        'Homisdallen Primary School - Kyebando',
        'Homisdallen Primary School - Kamwokya'
    ];

    $branches = [
        ['Homisdallen Primary School - Gayaza', 'Gayaza Road, Kampala', '+256 701 111111', 'gayaza@homisdallen.com'],
        ['Homisdallen Primary School - Kyebando', 'Kyebando Central, Kampala', '+256 702 222222', 'kyebando@homisdallen.com'],
        ['Homisdallen Primary School - Kamwokya', 'Kamwokya Hill, Kampala', '+256 703 333333', 'kamwokya@homisdallen.com']
    ];

    echo "<h3>Cleaning up unauthorized schools...</h3>";
    // Delete schools that are not in the allowed list
    $placeholders = str_repeat('?,', count($allowed_branch_names) - 1) . '?';
    $delete_stmt = $pdo->prepare("DELETE FROM schools WHERE school_name NOT IN ($placeholders)");
    $delete_stmt->execute($allowed_branch_names);
    $deleted_count = $delete_stmt->rowCount();
    if ($deleted_count > 0) {
        echo "<p style='color: orange;'>- Removed $deleted_count unauthorized school(s) (including 'Green Hill' if present).</p>";
    } else {
        echo "<p style='color: gray;'>• No unauthorized schools found.</p>";
    }

    echo "<h3>Checking School Branches...</h3>";
    foreach ($branches as $branch) {
        $stmt = $pdo->prepare("SELECT id FROM schools WHERE school_name = ?");
        $stmt->execute([$branch[0]]);
        if (!$stmt->fetch()) {
            $insert = $pdo->prepare("INSERT INTO schools (school_name, school_address, school_phone, school_email) VALUES (?, ?, ?, ?)");
            $insert->execute($branch);
            echo "<p style='color: blue;'>+ Added Branch: " . $branch[0] . "</p>";
        } else {
            echo "<p style='color: gray;'>• Branch already exists: " . $branch[0] . "</p>";
        }
    }

    // 4. Update table structure if columns missing
    echo "<h3>Updating Table Structures...</h3>";
    
    // Add Attendance table if missing
    $pdo->exec("CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        period_id INT,
        date DATE NOT NULL,
        status ENUM('present', 'absent', 'late', 'excused') DEFAULT 'present',
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (period_id) REFERENCES academic_periods(id) ON DELETE CASCADE,
        UNIQUE KEY student_date (student_id, date)
    )");
    echo "<p style='color: blue;'>+ Ensured 'attendance' table exists.</p>";

    // Ensure audit_logs has necessary columns and indexes
    $pdo->exec("CREATE TABLE IF NOT EXISTS audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(255) NOT NULL,
        table_name VARCHAR(50),
        record_id INT,
        old_values TEXT,
        new_values TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        INDEX (action),
        INDEX (created_at)
    )");
    echo "<p style='color: blue;'>+ Ensured 'audit_logs' table is optimized.</p>";

    $student_columns = [
        'address' => "TEXT",
        'guardian_place_of_work' => "VARCHAR(255)",
        'guardian_profession' => "VARCHAR(100)",
        'student_age' => "INT",
        'nationality' => "VARCHAR(50)",
        'student_status' => "VARCHAR(100)",
        'lin_number' => "VARCHAR(50)"
    ];

    foreach ($student_columns as $col => $type) {
        $check = $pdo->query("SHOW COLUMNS FROM students LIKE '$col'");
        if (!$check->fetch()) {
            $pdo->exec("ALTER TABLE students ADD COLUMN $col $type");
            echo "<p style='color: blue;'>+ Added column '$col' to students table.</p>";
        }
    }

    // 4. Test fetching schools
    $stmt = $pdo->query("SELECT * FROM schools");
    $schools = $stmt->fetchAll();
    echo "<h3>Total Schools Found: " . count($schools) . "</h3>";
    foreach ($schools as $school) {
        echo "- <strong>" . $school['school_name'] . "</strong> (" . $school['school_email'] . ")<br>";
    }

    // 3. Test fetching users
    $stmt = $pdo->query("SELECT username, role, full_name FROM users");
    $users = $stmt->fetchAll();
    echo "<h3>Users Found: " . count($users) . "</h3>";
    foreach ($users as $user) {
        echo "- " . $user['full_name'] . " (@" . $user['username'] . ") - Role: " . $user['role'] . "<br>";
    }

    // 5. Test Student details
    $stmt = $pdo->query("SELECT s.student_id_number, u.full_name, s.class_name, s.guardian_name, s.nationality 
                        FROM students s 
                        JOIN users u ON s.user_id = u.id");
    $students = $stmt->fetchAll();
    echo "<h3>Students Found: " . count($students) . "</h3>";
    foreach ($students as $student) {
        echo "- " . $student['full_name'] . " (ID: " . $student['student_id_number'] . ") - Class: " . $student['class_name'] . " - Guardian: " . $student['guardian_name'] . " - Nationality: " . $student['nationality'] . "<br>";
    }

    // 6. Fix Admin Credentials & Clear Rate Limits
    echo "<h3>Fixing Admin Credentials...</h3>";
    $known_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Get a valid school_id for the admin
    $school_stmt = $pdo->query("SELECT id FROM schools LIMIT 1");
    $school_data = $school_stmt->fetch();
    $valid_school_id = $school_data ? $school_data['id'] : null;

    // Ensure sysadmin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'sysadmin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO users (username, password, full_name, role, status) VALUES ('sysadmin', ?, 'System Administrator', 'system_admin', 'active')")
            ->execute([$known_password]);
        echo "<p style='color: blue;'>+ Re-created missing 'sysadmin' account.</p>";
    } else {
        $pdo->prepare("UPDATE users SET password = ?, status = 'active' WHERE username = 'sysadmin'")
            ->execute([$known_password]);
        echo "<p style='color: green;'>✓ Reset 'sysadmin' password to 'admin123'.</p>";
    }

    // Ensure homisdallen_admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'homisdallen_admin'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO users (school_id, username, password, full_name, role, status) VALUES (?, 'homisdallen_admin', ?, 'Gayaza Admin', 'school_admin', 'active')")
            ->execute([$valid_school_id, $known_password]);
        echo "<p style='color: blue;'>+ Re-created missing 'homisdallen_admin' account.</p>";
    } else {
        $pdo->prepare("UPDATE users SET password = ?, status = 'active', school_id = ? WHERE username = 'homisdallen_admin'")
            ->execute([$known_password, $valid_school_id]);
        echo "<p style='color: green;'>✓ Reset 'homisdallen_admin' password to 'admin123'.</p>";
    }

    // Clear rate limits
    $pdo->exec("DELETE FROM login_attempts");
    echo "<p style='color: green;'>✓ Cleared all login rate limits/blocks.</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database Error: " . $e->getMessage() . "</p>";
}
