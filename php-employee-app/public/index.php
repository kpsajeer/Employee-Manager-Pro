<?php

declare(strict_types=1);

/**
 * Front controller – single entry point for the entire application.
 *
 * Responsibilities:
 *   1. Bootstrap (constants, Composer autoload, session, config)
 *   2. Simple regex-based router
 *   3. Dispatch to the appropriate Controller action
 */

// ── Bootstrap ────────────────────────────────────────────────────────────────

define('ROOT_PATH', dirname(__DIR__));

// Composer PSR-4 autoloader
require ROOT_PATH . '/vendor/autoload.php';

// Application config
$appConfig = require ROOT_PATH . '/config/app.php';
date_default_timezone_set($appConfig['timezone']);

// Session – HttpOnly + SameSite=Lax for CSRF safety
$sessionName = $appConfig['session_name'];
session_name($sessionName);
session_set_cookie_params([
    'lifetime' => 0,           // browser-session cookie
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ── Helpers ──────────────────────────────────────────────────────────────────

use App\Controllers\DashboardController;
use App\Controllers\EmployeeController;

/**
 * Terminate with a plain-text 405 Method Not Allowed response.
 */
function methodNotAllowed(): never
{
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

/**
 * Terminate with a plain-text 404 Not Found response.
 */
function notFound(): never
{
    http_response_code(404);
    echo '404 – Page Not Found';
    exit;
}

// ── Routing ──────────────────────────────────────────────────────────────────

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Normalise trailing slash (except root)
if ($uri !== '/' && str_ends_with($uri, '/')) {
    $uri = rtrim($uri, '/');
}

// Routes table:  [ pattern, method(s), callable ]
$routes = [
    // Dashboard
    ['#^/$#',                          ['GET'],         fn() => (new DashboardController())->index()],

    // Employee list + export (export must be before /{id} to avoid matching 'export' as an id)
    ['#^/employees$#',                 ['GET'],         fn() => (new EmployeeController())->index()],
    ['#^/employees/export$#',          ['GET'],         fn() => (new EmployeeController())->export()],
    ['#^/employees/create$#',          ['GET'],         fn() => (new EmployeeController())->create()],
    ['#^/employees/store$#',           ['POST'],        fn() => (new EmployeeController())->store()],

    // Single employee
    ['#^/employees/(\d+)$#',           ['GET'],         fn($id) => (new EmployeeController())->show((int)$id)],
    ['#^/employees/(\d+)/edit$#',      ['GET'],         fn($id) => (new EmployeeController())->edit((int)$id)],
    ['#^/employees/(\d+)/update$#',    ['POST'],        fn($id) => (new EmployeeController())->update((int)$id)],
    ['#^/employees/(\d+)/delete$#',    ['POST'],        fn($id) => (new EmployeeController())->delete((int)$id)],
];

foreach ($routes as [$pattern, $methods, $handler]) {
    if (!preg_match($pattern, $uri, $matches)) {
        continue;
    }

    // URI matched – check HTTP method
    if (!in_array($method, $methods, true)) {
        methodNotAllowed();
    }

    // Call the handler, passing any captured URI segments as arguments
    array_shift($matches);          // remove full-match element
    $handler(...$matches);          // spread captures as positional args
    exit;
}

// No route matched
notFound();
