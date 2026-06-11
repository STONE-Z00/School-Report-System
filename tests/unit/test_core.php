<?php
/**
 * Unit Test: Functions and Models
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/models/User.php';

function run_test(string $name, callable $callback) {
    try {
        $result = $callback();
        echo "[PASS] $name\n";
    } catch (Exception $e) {
        echo "[FAIL] $name: " . $e->getMessage() . "\n";
    }
}

// 1. Test XSS Cleaning
run_test("XSS Protection Test", function() {
    $input = "<script>alert('xss')</script>";
    $output = clean_input($input);
    if (strpos($output, '<script>') !== false) {
        throw new Exception("XSS tags were not encoded.");
    }
    return true;
});

// 2. Test CSRF Token Test
run_test("CSRF Token Test", function() {
    // Session is handled by the test runner if needed
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    $token1 = generate_csrf_token();
    $token2 = generate_csrf_token();
    if (empty($token1) || $token1 !== $token2) {
        throw new Exception("CSRF token generation failed or is inconsistent.");
    }
    return true;
});

// 3. Test User Model Instance
run_test("User Model Instance Test", function() {
    global $pdo;
    $userModel = new User($pdo);
    if (!$userModel instanceof User) {
        throw new Exception("Failed to instantiate User model.");
    }
    return true;
});

echo "\nUnit Testing Complete.\n";
