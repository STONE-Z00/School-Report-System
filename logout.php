<?php
/**
 * Logout script
 */

require_once 'config/config.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    log_action($pdo, $_SESSION['user_id'], 'User Logout');
}

session_unset();
session_destroy();

header('Location: login.php');
exit();
