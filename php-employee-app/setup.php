<?php

/**
 * First-run database bootstrap.
 *
 * Called by start.sh before the PHP server starts.
 * Checks whether the employees table already exists; if not, creates the
 * full schema (table, indexes, trigger) and seeds 20 sample employees.
 *
 * Safe to run multiple times – uses IF NOT EXISTS / ON CONFLICT DO NOTHING.
 */

declare(strict_types=1);

define('ROOT_PATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config/database.php';

// Build DSN with libpq options preserved (same logic as Database.php)
$dsn = sprintf(
    'pgsql:host=%s;port=%d;dbname=%s',
    $config['host'],
    (int) $config['port'],
    $config['dbname']
);
foreach ($config['extra_options'] as $key => $value) {
    $dsn .= sprintf(';%s=%s', $key, $value);
}

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (\PDOException $e) {
    fwrite(STDERR, "[Setup] ERROR: Could not connect to PostgreSQL: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

// Check if the table already exists.
$exists = (int) $pdo->query(
    "SELECT COUNT(*) FROM information_schema.tables
     WHERE table_schema = 'public' AND table_name = 'employees'"
)->fetchColumn();

if ($exists > 0) {
    echo "[Setup] Schema already exists – skipping." . PHP_EOL;
    exit(0);
}

echo "[Setup] Creating schema and seeding data…" . PHP_EOL;

$sql = file_get_contents(__DIR__ . '/database.sql');

if ($sql === false) {
    fwrite(STDERR, "[Setup] ERROR: Could not read database.sql" . PHP_EOL);
    exit(1);
}

$pdo->exec($sql);

echo "[Setup] Done – 20 sample employees loaded." . PHP_EOL;
