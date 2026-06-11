<?php
/**
 * Forgot Password Page
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $email = clean_input($_POST['email']);

    if (empty($email)) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // In a real system, we would send an email here with a reset token.
                // For this project, we will simulate it.
                log_action($pdo, $user['id'], 'Password Reset Requested');
                $success = "A password reset link has been sent to <strong>" . $email . "</strong>. Please check your inbox.";
            } else {
                // For security, don't confirm if the email exists or not
                $success = "If that email exists in our system, you will receive a reset link shortly.";
            }
        } catch (PDOException $e) {
            $error = "An error occurred. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .forgot-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-reset { width: 100%; padding: 10px; background: #4a90e2; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .btn-reset:hover { background: #357abd; }
        .auth-links { margin-top: 15px; text-align: center; }
    </style>
</head>
<body class="auth-page">
    <div class="login-container">
        <h2 style="text-align: center;">Reset Password</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">Enter your email and we'll send you a link to reset your password.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="color: red; background: #fee2e2; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #fecaca;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="color: green; background: #dcfce7; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #bbf7d0;"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="forgot_password.php" method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="name@example.com">
            </div>
            <button type="submit" class="btn-reset">Send Reset Link</button>
        </form>

        <div class="auth-links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>