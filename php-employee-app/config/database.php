<?php

declare(strict_types=1);

/**
 * Database configuration.
 *
 * Replit environment: MySQL is started locally via start.sh.
 * The socket file is used for the connection (no network overhead).
 *
 * For local development outside Replit, switch to TCP:
 *   comment out 'socket' and set 'host'/'port' instead.
 */
return [
    // Unix socket path used when running on Replit (set by start.sh)
    'socket'   => '/tmp/mysql.sock',

    // TCP fallback – used when 'socket' is empty/absent
    'host'     => '127.0.0.1',
    'port'     => 3306,

    'dbname'   => 'employee_manager',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
