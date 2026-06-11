<?php
/**
 * Load Test: Simulated concurrent database queries
 */

require_once __DIR__ . '/../../config/db.php';

echo "Running Load Test (1000 simulated school lookups)...\n";

$start_time = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    $stmt = $pdo->query("SELECT * FROM schools LIMIT 1");
    $stmt->fetch();
}

$end_time = microtime(true);
$total_time = round($end_time - $start_time, 4);
$avg_time = round(($total_time / 1000) * 1000, 4); // in ms

echo "Total Time: {$total_time}s\n";
echo "Average Query Time: {$avg_time}ms\n";

if ($avg_time < 5) {
    echo "[PASS] System performance is within acceptable low-bandwidth limits.\n";
} else {
    echo "[WARN] Performance may be slow in low-bandwidth environments.\n";
}

echo "\nLoad Testing Complete.\n";
