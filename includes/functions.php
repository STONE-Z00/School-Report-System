<?php
/**
 * Global Utility Functions
 */

/**
 * Log an action to the database audit_logs table
 */
function log_action(PDO $pdo, int $user_id, string $action, $table_name = null, $record_id = null, $old_values = null, $new_values = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->execute([$user_id, $action, $table_name, $record_id, $old_values, $new_values, $ip]);
    } catch (PDOException $e) {
        // Fallback to file logging if database logging fails
        error_log("Audit Log Failure: " . $e->getMessage());
    }
}

/**
 * Check if user has required role
 */
function check_role($roles) {
    if (!isset($_SESSION['user_role'])) {
        header('Location: login.php');
        exit();
    }

    if (is_array($roles)) {
        if (!in_array($_SESSION['user_role'], $roles)) {
            $_SESSION['error'] = "Unauthorized access.";
            header('Location: index.php');
            exit();
        }
    } else {
        if ($_SESSION['user_role'] !== $roles) {
            $_SESSION['error'] = "Unauthorized access.";
            header('Location: index.php');
            exit();
        }
    }
}

/**
 * Clean user input (XSS Protection)
 */
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * CSRF Hidden Input Field
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Rate Limiting: Check if IP is blocked
 */
function is_rate_limited($pdo) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
    $stmt->execute([$ip]);
    $record = $stmt->fetch();

    if ($record) {
        $attempts = $record['attempts'];
        $last_attempt = strtotime($record['last_attempt']);
        $now = time();

        // Block for 15 minutes after 5 failed attempts
        if ($attempts >= 5 && ($now - $last_attempt) < 900) {
            return true;
        }
        
        // Reset if more than 15 mins have passed
        if (($now - $last_attempt) >= 900) {
            $reset = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
            $reset->execute([$ip]);
        }
    }
    return false;
}

/**
 * Rate Limiting: Record failed attempt
 */
function record_failed_attempt($pdo) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, attempts) VALUES (?, 1) 
                           ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP");
    $stmt->execute([$ip]);
}

/**
 * Rate Limiting: Reset attempts on success
 */
function reset_login_attempts($pdo) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
    $stmt->execute([$ip]);
}

/**
 * Redirect with message
 */
function redirect(string $url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION[$type] = $message;
    }
    header("Location: $url");
    exit();
}
