<?php
/**
 * API: Get Users by School
 */
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/models/User.php';

// Check if logged in and authorized
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['system_admin', 'school_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$school_id = $_GET['school_id'] ?? $_SESSION['school_id'];

if (!$school_id && $_SESSION['user_role'] !== 'system_admin') {
    echo json_encode(['success' => false, 'message' => 'School ID required']);
    exit();
}

$userModel = new User($pdo);
$users = $userModel->getBySchool($school_id);

echo json_encode(['success' => true, 'data' => $users]);
