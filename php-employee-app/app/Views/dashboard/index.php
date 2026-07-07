<?php declare(strict_types=1);
/** Dashboard – summary statistics overview. */
require ROOT_PATH . '/app/Views/layout/header.php';
?>

<!-- ── Stat cards ──────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#ede9fe;">
                    <i class="bi bi-people-fill" style="color:#7c3aed;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.06em;">Total Employees</div>
                    <div class="fs-2 fw-bold lh-1 mt-1"><?= $stats['total'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#d1fae5;">
                    <i class="bi bi-person-check-fill" style="color:#059669;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.06em;">Active</div>
                    <div class="fs-2 fw-bold lh-1 mt-1"><?= $stats['active'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fee2e2;">
                    <i class="bi bi-person-x-fill" style="color:#dc2626;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.06em;">Inactive</div>
                    <div class="fs-2 fw-bold lh-1 mt-1"><?= $stats['inactive'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#dbeafe;">
                    <i class="bi bi-building-fill" style="color:#1d4ed8;"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:.72rem;letter-spacing:.06em;">Departments</div>
                    <div class="fs-2 fw-bold lh-1 mt-1"><?= $stats['departments'] ?></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ── Quick-action cards ──────────────────────────────────────────── -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                Quick Actions
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="/employees/create" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill"></i> Add New Employee
                </a>
                <a href="/employees" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-table"></i> View All Employees
                </a>
                <a href="/employees/export" class="btn btn-outline-success d-flex align-items-center gap-2">
                    <i class="bi bi-download"></i> Export to CSV
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-fill text-primary"></i>
                Workforce Summary
            </div>
            <div class="card-body">
                <?php if ($stats['total'] > 0): ?>
                    <?php $activePct = round(($stats['active'] / $stats['total']) * 100); ?>
                    <p class="text-muted small mb-2">Active employees: <strong><?= $activePct ?>%</strong></p>
                    <div class="progress mb-3" style="height:10px; border-radius:99px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width:<?= $activePct ?>%"
                             aria-valuenow="<?= $activePct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted small mb-2">Inactive employees: <strong><?= 100 - $activePct ?>%</strong></p>
                    <div class="progress" style="height:10px; border-radius:99px;">
                        <div class="progress-bar bg-danger" role="progressbar"
                             style="width:<?= 100 - $activePct ?>%"
                             aria-valuenow="<?= 100 - $activePct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No employees yet. <a href="/employees/create">Add one now</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/layout/footer.php'; ?>
