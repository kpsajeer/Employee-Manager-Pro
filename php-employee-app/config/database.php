<?php

declare(strict_types=1);

/**
 * Database configuration – PostgreSQL via Replit's built-in database.
 *
 * DATABASE_URL is provisioned automatically by Replit and is available
 * in both development and production.
 *
 * Format:  postgresql://user:password@host:port/dbname[?sslmode=...]
 */

$url = getenv('DATABASE_URL');

if (empty($url)) {
    throw new \RuntimeException(
        'DATABASE_URL environment variable is not set. ' .
        'Make sure a Replit PostgreSQL database is provisioned for this project.'
    );
}

$parsed = parse_url($url);

if ($parsed === false || empty($parsed['host'])) {
    throw new \RuntimeException('DATABASE_URL is not a valid URL.');
}

// Decode percent-encoded characters in credentials (e.g. passwords with @, /, etc.)
$username = rawurldecode($parsed['user'] ?? '');
$password = rawurldecode($parsed['pass'] ?? '');

// Extract any libpq connection options from the URL query string (e.g. sslmode=require)
$extraOptions = [];
if (!empty($parsed['query'])) {
    parse_str($parsed['query'], $extraOptions);
}

return [
    'host'         => $parsed['host'],
    'port'         => $parsed['port']          ?? 5432,
    'dbname'       => ltrim($parsed['path'] ?? '', '/'),
    'username'     => $username,
    'password'     => $password,
    'extra_options' => $extraOptions,   // e.g. ['sslmode' => 'require']

    'pdo_options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Emulate prepares so the same named placeholder (e.g. :search)
        // can appear multiple times in one query — required for PostgreSQL.
        PDO::ATTR_EMULATE_PREPARES   => true,
    ],
];
