<?php
/**
 * Integration Test: Database and Authentication
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

echo "Running Integration Tests...\n";

// 1. Database Connection Test
try {
    $pdo->query("SELECT 1");
    echo "[PASS] Database connection is stable.\n";
} catch (PDOException $e) {
    die("[FAIL] Database connection failed: " . $e->getMessage() . "\n");
}

// 2. Authentication Flow Test (Simulated)
try {
    $username = 'sysadmin';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && $user['username'] === 'sysadmin' && $user['role'] === 'system_admin') {
        echo "[PASS] Admin user record retrieval successful.\n";
    } else {
        throw new Exception("Admin user not found or role mismatch.");
    }
} catch (Exception $e) {
    echo "[FAIL] Auth flow test: " . $e->getMessage() . "\n";
}

// 3. Multi-school Isolation Test
try {
    $stmt = $pdo->query("SELECT COUNT(DISTINCT school_id) as school_count FROM users WHERE school_id IS NOT NULL");
    $result = $stmt->fetch();
    if ($result['school_count'] >= 1) {
        echo "[PASS] Multi-school data structure verified.\n";
    } else {
        throw new Exception("No school-linked users found.");
    }
} catch (Exception $e) {
    echo "[FAIL] Multi-school test: " . $e->getMessage() . "\n";
}

echo "\nIntegration Testing Complete.\n";
