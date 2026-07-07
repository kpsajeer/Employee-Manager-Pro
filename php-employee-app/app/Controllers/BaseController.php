<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * BaseController – shared utilities for all controllers.
 *
 * Provides view rendering, flash messages, CSRF helpers,
 * redirects, and basic input sanitisation.
 */
abstract class BaseController
{
    // -------------------------------------------------------------------------
    // View rendering
    // -------------------------------------------------------------------------

    /**
     * Render a view file with the given data.
     *
     * The view receives every key in $data as a local variable.
     * Header and footer are NOT included here so individual views
     * can control the exact layout they need.
     *
     * @param  string              $view  Dot-notation path relative to app/Views/ (e.g. 'employees.index')
     * @param  array<string,mixed> $data
     */
    protected function render(string $view, array $data = []): void
    {
        $viewPath = ROOT_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View not found: $viewPath";
            return;
        }

        // Make config helpers available to every view
        $data['appConfig'] = require ROOT_PATH . '/config/app.php';

        extract($data, EXTR_SKIP);
        require $viewPath;
    }

    // -------------------------------------------------------------------------
    // Flash messages (single-use session messages)
    // -------------------------------------------------------------------------

    /**
     * Store a flash message of the given type in the session.
     *
     * @param 'success'|'error'|'warning'|'info' $type
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type][] = $message;
    }

    // -------------------------------------------------------------------------
    // Redirect
    // -------------------------------------------------------------------------

    /**
     * Perform an HTTP redirect (302 by default) and halt execution.
     */
    protected function redirect(string $url, int $code = 302): never
    {
        header("Location: $url", true, $code);
        exit;
    }

    // -------------------------------------------------------------------------
    // CSRF protection
    // -------------------------------------------------------------------------

    /**
     * Generate a CSRF token (stored in the session) and return it.
     * A new token is generated per session, not per request, for usability.
     */
    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify that the submitted _csrf_token matches the session token.
     * Terminates the request with 403 on mismatch.
     */
    protected function verifyCsrf(): void
    {
        $submitted = $_POST['_csrf_token'] ?? '';

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $submitted)
        ) {
            http_response_code(403);
            exit('Forbidden – CSRF token mismatch.');
        }
    }

    // -------------------------------------------------------------------------
    // Input helpers
    // -------------------------------------------------------------------------

    /**
     * Return a trimmed, HTML-entity-safe string from $_POST.
     */
    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;
        return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Return a raw (not HTML-encoded) value from $_POST.
     * Use only when encoding will happen downstream (e.g. in the model).
     */
    protected function rawInput(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

    /**
     * Validate employee form data.
     *
     * Returns an array of error messages indexed by field name.
     * An empty array means validation passed.
     *
     * @param  array<string,string> $data
     * @return array<string,string>
     */
    protected function validateEmployee(array $data): array
    {
        $errors    = [];
        $appConfig = require ROOT_PATH . '/config/app.php';

        // Required fields
        foreach (['first_name', 'last_name', 'email', 'department', 'position', 'date_joined'] as $field) {
            if (trim($data[$field] ?? '') === '') {
                $errors[$field] = ucwords(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        if (!isset($errors['email']) && !filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if (!isset($errors['first_name']) && strlen($data['first_name']) > 100) {
            $errors['first_name'] = 'First name must be 100 characters or fewer.';
        }

        if (!isset($errors['last_name']) && strlen($data['last_name']) > 100) {
            $errors['last_name'] = 'Last name must be 100 characters or fewer.';
        }

        if (!empty($data['phone']) && !preg_match('/^[\d\s\+\-\(\)\.]{7,20}$/', $data['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }

        if (!isset($errors['department']) && !in_array($data['department'] ?? '', $appConfig['departments'], true)) {
            $errors['department'] = 'Please select a valid department.';
        }

        $salary = $data['salary'] ?? '';
        if ($salary === '' || !is_numeric($salary) || (float)$salary < 0) {
            $errors['salary'] = 'Please enter a valid salary (0 or greater).';
        }

        if (!isset($errors['date_joined'])) {
            $d = \DateTime::createFromFormat('Y-m-d', $data['date_joined'] ?? '');
            if (!$d || $d->format('Y-m-d') !== $data['date_joined']) {
                $errors['date_joined'] = 'Please enter a valid date (YYYY-MM-DD).';
            }
        }

        if (!in_array($data['status'] ?? '', ['active', 'inactive'], true)) {
            $errors['status'] = 'Status must be Active or Inactive.';
        }

        return $errors;
    }
}
