<?php
/**
 * Global Configuration
 */

define('APP_NAME', 'Digital School Report System');
define('APP_URL', 'http://localhost/SystemProjects/SchoolReportSystem');
define('LOG_PATH', __DIR__ . '/../logs/system.log');

// Secure Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Nairobi'); // Adjust as per school location
