<?php
/**
 * Login Page
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    // Rate Limiting
    if (is_rate_limited($pdo)) {
        $error = "Too many failed attempts. Please try again in 15 minutes.";
    } else {
        $username = clean_input($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Reset login attempts on success
                    reset_login_attempts($pdo);

                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['school_id'] = $user['school_id'];

                    // Log the login action
                    log_action($pdo, $user['id'], 'User Login');

                    // Update last login
                    $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $update->execute([$user['id']]);

                    redirect('index.php', "Welcome back, " . $user['full_name']);
                } else {
                    $error = "Invalid username or password.";
                    // Record failed attempt for rate limiting
                    record_failed_attempt($pdo);
                    // Log failed attempt
                    log_action($pdo, 0, "Failed Login Attempt for: $username");
                }
            } catch (PDOException $e) {
                $error = "An error occurred. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <div class="login-container">
        <h2><?php echo APP_NAME; ?></h2>
        <?php if ($error): ?>
            <div class="alert alert-danger" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="color: green; margin-bottom: 15px;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="username">ID Number / Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="auth-links" style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 15px;">
            <p><a href="forgot_password.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">Forgot Password?</a></p>
            <p style="margin-top: 10px;">New to the system? <a href="register.php" style="color: #4a90e2; font-weight: bold; text-decoration: none;">Register here</a></p>
        </div>
    </div>
</body>
</html>
