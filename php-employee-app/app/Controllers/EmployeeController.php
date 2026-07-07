<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Employee;

/**
 * EmployeeController – handles all CRUD actions for employees.
 *
 * Routes (matched in public/index.php):
 *   GET  /employees              → index()
 *   GET  /employees/create       → create()
 *   POST /employees/store        → store()
 *   GET  /employees/{id}         → show()
 *   GET  /employees/{id}/edit    → edit()
 *   POST /employees/{id}/update  → update()
 *   POST /employees/{id}/delete  → delete()
 *   GET  /employees/export       → export()
 */
class EmployeeController extends BaseController
{
    private Employee $employeeModel;

    public function __construct()
    {
        $this->employeeModel = new Employee();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    public function index(): void
    {
        $params = [
            'search'     => trim($_GET['search']     ?? ''),
            'department' => trim($_GET['department'] ?? ''),
            'sort'       => $_GET['sort']  ?? 'id',
            'order'      => $_GET['order'] ?? 'ASC',
            'page'       => $_GET['page']  ?? '1',
        ];

        $result     = $this->employeeModel->paginate($params);
        $appConfig  = require ROOT_PATH . '/config/app.php';
        $totalPages = (int) ceil($result['total'] / $appConfig['pagination_limit']);

        $this->render('employees.index', [
            'title'       => 'Employees',
            'employees'   => $result['rows'],
            'total'       => $result['total'],
            'totalPages'  => $totalPages,
            'params'      => $params,
            'csrfToken'   => $this->csrfToken(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): void
    {
        $this->render('employees.create', [
            'title'     => 'Add Employee',
            'employee'  => [],
            'errors'    => [],
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $data = $this->collectFormData();

        // Check for duplicate email (pre-flight; also caught at DB level below)
        if ($this->employeeModel->emailExists($data['email'])) {
            $errors = ['email' => 'This email address is already in use.'];
            $this->render('employees.create', [
                'title'     => 'Add Employee',
                'employee'  => $data,
                'errors'    => $errors,
                'csrfToken' => $this->csrfToken(),
            ]);
            return;
        }

        $errors = $this->validateEmployee($data);

        if ($errors !== []) {
            $this->render('employees.create', [
                'title'     => 'Add Employee',
                'employee'  => $data,
                'errors'    => $errors,
                'csrfToken' => $this->csrfToken(),
            ]);
            return;
        }

        try {
            $id = $this->employeeModel->create($data);
        } catch (\RuntimeException $e) {
            // Unique key race condition between pre-check and insert
            if ($e->getMessage() === 'duplicate_email') {
                $errors = ['email' => 'This email address is already in use.'];
                $this->render('employees.create', [
                    'title'     => 'Add Employee',
                    'employee'  => $data,
                    'errors'    => $errors,
                    'csrfToken' => $this->csrfToken(),
                ]);
                return;
            }
            throw $e;
        }

        $this->flash('success', 'Employee created successfully.');
        $this->redirect('/employees/' . $id);
    }

    // -------------------------------------------------------------------------
    // Read (detail)
    // -------------------------------------------------------------------------

    public function show(int $id): void
    {
        $employee = $this->employeeModel->findById($id);

        if ($employee === null) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $this->render('employees.show', [
            'title'     => 'Employee Details',
            'employee'  => $employee,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(int $id): void
    {
        $employee = $this->employeeModel->findById($id);

        if ($employee === null) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $this->render('employees.edit', [
            'title'     => 'Edit Employee',
            'employee'  => $employee,
            'errors'    => [],
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function update(int $id): void
    {
        $this->verifyCsrf();

        $employee = $this->employeeModel->findById($id);
        if ($employee === null) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $data   = $this->collectFormData();
        $errors = $this->validateEmployee($data);

        // Check for duplicate email (excluding current record)
        if (!isset($errors['email']) && $this->employeeModel->emailExists($data['email'], $id)) {
            $errors['email'] = 'This email address is already in use by another employee.';
        }

        if ($errors !== []) {
            $data['id'] = $id;
            $this->render('employees.edit', [
                'title'     => 'Edit Employee',
                'employee'  => $data,
                'errors'    => $errors,
                'csrfToken' => $this->csrfToken(),
            ]);
            return;
        }

        try {
            $this->employeeModel->update($id, $data);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'duplicate_email') {
                $errors['email'] = 'This email address is already in use by another employee.';
                $data['id'] = $id;
                $this->render('employees.edit', [
                    'title'     => 'Edit Employee',
                    'employee'  => $data,
                    'errors'    => $errors,
                    'csrfToken' => $this->csrfToken(),
                ]);
                return;
            }
            throw $e;
        }

        $this->flash('success', 'Employee updated successfully.');
        $this->redirect('/employees/' . $id);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function delete(int $id): void
    {
        $this->verifyCsrf();

        $employee = $this->employeeModel->findById($id);
        if ($employee === null) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $this->employeeModel->delete($id);
        $this->flash('success', 'Employee deleted successfully.');
        $this->redirect('/employees');
    }

    // -------------------------------------------------------------------------
    // CSV Export
    // -------------------------------------------------------------------------

    public function export(): void
    {
        $params = [
            'search'     => trim($_GET['search']     ?? ''),
            'department' => trim($_GET['department'] ?? ''),
        ];

        $employees = $this->employeeModel->exportAll($params);

        // Stream as CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="employees_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // BOM for Excel UTF-8 compatibility
        fwrite($out, "\xEF\xBB\xBF");

        // Header row
        fputcsv($out, [
            'ID', 'First Name', 'Last Name', 'Email', 'Phone',
            'Department', 'Position', 'Salary', 'Date Joined', 'Status', 'Created At',
        ]);

        foreach ($employees as $row) {
            fputcsv($out, [
                $row['id'],
                $this->sanitizeCsvCell($row['first_name']),
                $this->sanitizeCsvCell($row['last_name']),
                $this->sanitizeCsvCell($row['email']),
                $this->sanitizeCsvCell($row['phone'] ?? ''),
                $this->sanitizeCsvCell($row['department']),
                $this->sanitizeCsvCell($row['position']),
                number_format((float) $row['salary'], 2, '.', ''),
                $row['date_joined'],
                ucfirst($row['status']),
                $row['created_at'],
            ]);
        }

        fclose($out);
        exit;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Neutralise CSV formula injection.
     *
     * Values beginning with =, +, -, @, TAB, or CR can be interpreted
     * as formulas by Excel / Google Sheets. Prefix with a single quote
     * to force the cell to be treated as plain text.
     */
    private function sanitizeCsvCell(string $value): string
    {
        $triggers = ['=', '+', '-', '@', "\t", "\r"];
        foreach ($triggers as $char) {
            if (str_starts_with($value, $char)) {
                return "'" . $value;
            }
        }
        return $value;
    }

    /**
     * Collect and lightly normalise POST form data.
     *
     * @return array<string,string>
     */
    private function collectFormData(): array
    {
        return [
            'first_name'  => $this->rawInput('first_name'),
            'last_name'   => $this->rawInput('last_name'),
            'email'       => strtolower($this->rawInput('email')),
            'phone'       => $this->rawInput('phone'),
            'department'  => $this->rawInput('department'),
            'position'    => $this->rawInput('position'),
            'salary'      => $this->rawInput('salary', '0'),
            'date_joined' => $this->rawInput('date_joined'),
            'status'      => $this->rawInput('status', 'active'),
        ];
    }
}
