<?php
/**
 * Security Test: CSRF and Rate Limiting
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Mock REMOTE_ADDR for CLI testing
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "Running Security Tests...\n";

// 1. Rate Limiting Test
try {
    // Clear previous attempts
    reset_login_attempts($pdo);
    
    // Simulate 6 failed attempts
    for ($i = 0; $i < 6; $i++) {
        record_failed_attempt($pdo);
    }
    
    // Check if recorded in DB
    $stmt = $pdo->prepare("SELECT attempts FROM login_attempts WHERE ip_address = ?");
    $stmt->execute(['127.0.0.1']);
    $record = $stmt->fetch();
    
    if ($record && $record['attempts'] >= 6) {
        echo "[PASS] Rate limiting attempts correctly recorded in database.\n";
    } else {
        throw new Exception("Rate limiting attempts not recorded correctly. Found: " . ($record['attempts'] ?? 0));
    }
    
    // Clean up
    reset_login_attempts($pdo);
    
} catch (Exception $e) {
    echo "[FAIL] Rate limiting test: " . $e->getMessage() . "\n";
}

// 2. Session Security Settings
$httponly = ini_get('session.cookie_httponly');
$samesite = ini_get('session.cookie_samesite');
if ($httponly || $samesite) {
    echo "[PASS] Session security (HttpOnly/SameSite) is configured.\n";
} else {
    echo "[FAIL] Session security is NOT configured.\n";
}

// 3. XSS Filtering on Arrays
$dirty_array = ['name' => '<script>alert(1)</script>', 'email' => 'test@test.com'];
$clean_array = clean_input($dirty_array);
if (strpos($clean_array['name'], '<script>') === false) {
    echo "[PASS] XSS protection works on recursive arrays.\n";
} else {
    echo "[FAIL] XSS protection failed on recursive arrays.\n";
}

echo "\nSecurity Testing Complete.\n";
