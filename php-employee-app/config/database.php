<?php

declare(strict_types=1);

/**
 * Database configuration
 * Update these values to match your local MySQL installation.
 */
return [
    'host'     => 'localhost',
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
