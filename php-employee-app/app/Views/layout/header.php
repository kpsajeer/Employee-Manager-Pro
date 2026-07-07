<?php declare(strict_types=1);
/**
 * Shared header / top-of-page layout template.
 *
 * Expected variables:
 *   string $title      – page title
 *   array  $appConfig  – application configuration array
 */

// Render and then clear flash messages
$flashMessages = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Employee Manager', ENT_QUOTES) ?> – <?= htmlspecialchars($appConfig['name'], ENT_QUOTES) ?></title>

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 240px;
            --sidebar-bg: #1a1d23;
            --sidebar-accent: #4f46e5;
            --sidebar-text: #a0aec0;
            --sidebar-text-active: #ffffff;
            --topbar-h: 56px;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ── Sidebar ─────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            overflow-y: auto;
            transition: transform .25s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: 1.25rem 1.25rem 1rem;
            color: #fff;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-brand .brand-icon {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: var(--sidebar-accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-nav { padding: .75rem 0; flex: 1; }

        .sidebar-label {
            font-size: .68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #4a5568;
            padding: 1rem 1.25rem .4rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .55rem 1.25rem;
            color: var(--sidebar-text);
            font-size: .875rem;
            border-radius: 0;
            transition: background .15s, color .15s;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: var(--sidebar-text-active);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(79, 70, 229, .18);
            color: var(--sidebar-text-active);
            border-left: 3px solid var(--sidebar-accent);
        }

        .sidebar-nav .nav-link i { font-size: 1rem; }

        /* ── Main content ────────────────────────── */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #topbar {
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            gap: 1rem;
        }

        #topbar .page-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1d23;
            flex: 1;
        }

        #page-content { padding: 1.75rem 1.5rem; flex: 1; }

        /* ── Cards ───────────────────────────────── */
        .card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f3f4f6;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }

        /* ── Stat cards ──────────────────────────── */
        .stat-card { border-radius: 12px; overflow: hidden; }

        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }

        /* ── Tables ──────────────────────────────── */
        .table thead th {
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6b7280;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .table tbody td { vertical-align: middle; font-size: .875rem; }
        .table tbody tr:hover { background: #fafafa; }
        .table th a { color: inherit; text-decoration: none; }
        .table th a:hover { color: var(--sidebar-accent); }

        /* ── Sort arrows ─────────────────────────── */
        .sort-asc::after  { content: ' ▲'; font-size: .65rem; }
        .sort-desc::after { content: ' ▼'; font-size: .65rem; }

        /* ── Badges ──────────────────────────────── */
        .badge-active   { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }

        /* ── Responsive sidebar toggle ───────────── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-wrapper { margin-left: 0; }
            .sidebar-backdrop {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.4);
                z-index: 1039;
            }
            .sidebar-backdrop.show { display: block; }
        }
    </style>
</head>
<body>

<!-- ═══ Sidebar ════════════════════════════════════════════════════════════ -->
<nav id="sidebar">
    <a href="/" class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-people-fill text-white"></i></span>
        EmployeeHub
    </a>

    <div class="sidebar-nav">
        <div class="sidebar-label">Menu</div>

        <a href="/"
           class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' ? 'active' : '') ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="/employees"
           class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/employees') ? 'active' : '') ?>">
            <i class="bi bi-people"></i> Employees
        </a>

        <a href="/employees/create" class="nav-link">
            <i class="bi bi-person-plus"></i> Add Employee
        </a>

        <div class="sidebar-label mt-2">Export</div>

        <a href="/employees/export" class="nav-link">
            <i class="bi bi-filetype-csv"></i> Export CSV
        </a>
    </div>
</nav>

<!-- Backdrop for mobile -->
<div class="sidebar-backdrop" id="sidebar-backdrop" onclick="toggleSidebar()"></div>

<!-- ═══ Main content wrapper ════════════════════════════════════════════════ -->
<div id="main-wrapper">
    <header id="topbar">
        <!-- Mobile hamburger -->
        <button class="btn btn-sm btn-light d-lg-none me-2" onclick="toggleSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>

        <span class="page-title"><?= htmlspecialchars($title ?? '', ENT_QUOTES) ?></span>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small d-none d-sm-inline">
                <?= date('D, d M Y') ?>
            </span>
        </div>
    </header>

    <main id="page-content">

        <?php foreach (['success', 'error', 'warning', 'info'] as $type): ?>
            <?php foreach ($flashMessages[$type] ?? [] as $msg): ?>
                <?php
                $alertClass = match($type) {
                    'success' => 'alert-success',
                    'error'   => 'alert-danger',
                    'warning' => 'alert-warning',
                    default   => 'alert-info',
                };
                $icon = match($type) {
                    'success' => 'bi-check-circle-fill',
                    'error'   => 'bi-x-circle-fill',
                    'warning' => 'bi-exclamation-triangle-fill',
                    default   => 'bi-info-circle-fill',
                };
                ?>
                <div class="alert <?= $alertClass ?> alert-dismissible d-flex align-items-center gap-2 fade show mb-3"
                     role="alert">
                    <i class="bi <?= $icon ?>"></i>
                    <span><?= htmlspecialchars($msg, ENT_QUOTES) ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
