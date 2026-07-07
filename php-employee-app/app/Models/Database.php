<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database – PDO singleton wrapper.
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

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            // Avoid leaking credentials in error messages
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
