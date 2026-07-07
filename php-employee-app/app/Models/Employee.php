<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Employee model – all database interactions for the employees table.
 *
 * Uses PDO prepared statements throughout to prevent SQL injection.
 */
class Employee
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * Fetch a paginated, filtered, and sorted list of employees.
     *
     * @param  array<string,string> $params  Query parameters (search, department, sort, order, page)
     * @return array{rows: list<array<string,mixed>>, total: int}
     */
    public function paginate(array $params): array
    {
        $appConfig   = require ROOT_PATH . '/config/app.php';
        $limit       = $appConfig['pagination_limit'];
        $page        = max(1, (int) ($params['page'] ?? 1));
        $offset      = ($page - 1) * $limit;
        $search      = trim($params['search'] ?? '');
        $department  = trim($params['department'] ?? '');

        // Whitelist of sortable columns to prevent column-injection
        $allowedSort = [
            'id', 'first_name', 'last_name', 'email', 'phone',
            'department', 'position', 'salary', 'date_joined', 'status',
        ];
        $sortCol   = in_array($params['sort'] ?? '', $allowedSort, true)
            ? $params['sort']
            : 'id';
        $sortOrder = strtoupper($params['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $where  = [];
        $binds  = [];

        if ($search !== '') {
            $where[] = "(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $binds[':search'] = '%' . $search . '%';
        }

        if ($department !== '') {
            $where[]               = "department = :department";
            $binds[':department']  = $department;
        }

        $whereClause = $where !== [] ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count total matching rows
        $countSql = "SELECT COUNT(*) FROM employees $whereClause";
        $stmt     = $this->db->prepare($countSql);
        $stmt->execute($binds);
        $total = (int) $stmt->fetchColumn();

        // Fetch page rows
        $dataSql = "SELECT * FROM employees $whereClause
                    ORDER BY $sortCol $sortOrder
                    LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($dataSql);
        foreach ($binds as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'rows'  => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    /**
     * Find a single employee by primary key. Returns null if not found.
     *
     * @return array<string,mixed>|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM employees WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    /**
     * Insert a new employee record.
     *
     * @param  array<string,mixed> $data  Validated employee data
     * @throws \RuntimeException if a duplicate email race condition is detected
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO employees
                    (first_name, last_name, email, phone, department, position,
                     salary, date_joined, status)
                VALUES
                    (:first_name, :last_name, :email, :phone, :department, :position,
                     :salary, :date_joined, :status)";

        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute($this->buildBinds($data));
        } catch (\PDOException $e) {
            // MySQL error 1062 = Duplicate entry (unique constraint violation)
            if ($e->getCode() === '23000') {
                throw new \RuntimeException('duplicate_email');
            }
            throw $e;
        }

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing employee record.
     *
     * @param  array<string,mixed> $data  Validated employee data
     * @throws \RuntimeException if a duplicate email race condition is detected
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE employees
                SET first_name  = :first_name,
                    last_name   = :last_name,
                    email       = :email,
                    phone       = :phone,
                    department  = :department,
                    position    = :position,
                    salary      = :salary,
                    date_joined = :date_joined,
                    status      = :status,
                    updated_at  = CURRENT_TIMESTAMP
                WHERE id = :id";

        $binds        = $this->buildBinds($data);
        $binds[':id'] = $id;

        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute($binds);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new \RuntimeException('duplicate_email');
            }
            throw $e;
        }

        return $stmt->rowCount() > 0;
    }

    /**
     * Delete an employee record by primary key.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM employees WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // -------------------------------------------------------------------------
    // Dashboard stats
    // -------------------------------------------------------------------------

    /** @return array<string,int> */
    public function getStats(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'active')   AS active,
                SUM(status = 'inactive') AS inactive,
                COUNT(DISTINCT department) AS departments
             FROM employees"
        );
        $row = $stmt->fetch();

        return [
            'total'       => (int) ($row['total']       ?? 0),
            'active'      => (int) ($row['active']      ?? 0),
            'inactive'    => (int) ($row['inactive']    ?? 0),
            'departments' => (int) ($row['departments'] ?? 0),
        ];
    }

    // -------------------------------------------------------------------------
    // Export
    // -------------------------------------------------------------------------

    /**
     * Fetch all employees for CSV export (respects current search/filter).
     *
     * @param  array<string,string> $params
     * @return list<array<string,mixed>>
     */
    public function exportAll(array $params): array
    {
        $search     = trim($params['search'] ?? '');
        $department = trim($params['department'] ?? '');

        $where = [];
        $binds = [];

        if ($search !== '') {
            $where[] = "(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $binds[':search'] = '%' . $search . '%';
        }
        if ($department !== '') {
            $where[] = "department = :department";
            $binds[':department'] = $department;
        }

        $whereClause = $where !== [] ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->db->prepare(
            "SELECT id, first_name, last_name, email, phone, department, position,
                    salary, date_joined, status, created_at
             FROM employees $whereClause ORDER BY id ASC"
        );
        $stmt->execute($binds);
        return $stmt->fetchAll();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Check whether an email already exists (optionally excluding a given employee id).
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM employees WHERE email = :email AND id != :id"
            );
            $stmt->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM employees WHERE email = :email"
            );
            $stmt->execute([':email' => $email]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Build the parameter array shared between create() and update().
     *
     * Values are stored RAW (no HTML encoding). Encoding belongs exclusively
     * in the view layer via htmlspecialchars(). Storing encoded values causes
     * double-encoding and corrupts search / comparison operations.
     *
     * @param  array<string,mixed>      $data
     * @return array<string,mixed>
     */
    private function buildBinds(array $data): array
    {
        return [
            ':first_name'  => trim((string) $data['first_name']),
            ':last_name'   => trim((string) $data['last_name']),
            ':email'       => strtolower(trim((string) $data['email'])),
            ':phone'       => trim((string) ($data['phone'] ?? '')),
            ':department'  => trim((string) $data['department']),
            ':position'    => trim((string) $data['position']),
            ':salary'      => (float) $data['salary'],
            ':date_joined' => $data['date_joined'],
            ':status'      => in_array($data['status'], ['active', 'inactive'], true)
                                ? $data['status']
                                : 'active',
        ];
    }
}
