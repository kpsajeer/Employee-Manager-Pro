<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database – PDO singleton wrapper for PostgreSQL.
 *
 * Provides a single shared connection for the entire request lifecycle.
 * Call Database::getInstance() anywhere you need the PDO object.
 */
final class Database
{
    private static ?self $instance = null;
    private readonly PDO $pdo;

    private function __construct()
    {
        $config = require ROOT_PATH . '/config/database.php';

        // Build DSN from parsed URL components, preserving any libpq options
        // (e.g. sslmode=require) that were present in the original DATABASE_URL.
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            $config['host'],
            (int) $config['port'],
            $config['dbname']
        );

        // Append libpq key=value options from the URL query string (sslmode, etc.)
        foreach ($config['extra_options'] as $key => $value) {
            $dsn .= sprintf(';%s=%s', $key, $value);
        }

        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['pdo_options']
            );
        } catch (PDOException $e) {
            // Avoid leaking credentials in error messages.
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /** Return the shared Database instance. */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Expose the underlying PDO object. */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Prevent cloning and unserialization of the singleton
    private function __clone() {}
    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize a singleton.');
    }
}
