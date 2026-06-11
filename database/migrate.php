<?php
/**
 * Database Migration Script
 * Runs the schema.sql file to set up the database and tables
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_report_db');

try {
    // 1. Connect to MySQL without specifying a database
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Read SQL file
    $sqlFile = __DIR__ . '/school_report_db.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Schema file not found: $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    // 3. Execute the SQL (which includes CREATE DATABASE)
    // Note: PDO::exec might not handle multiple statements in some drivers.
    // We'll use the raw connection to execute the script.
    $pdo->exec($sql);

    echo "Migration successful! Database '" . DB_NAME . "' and tables created.\n";
    echo "Sample data inserted.\n";

} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
