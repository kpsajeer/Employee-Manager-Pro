<?php declare(strict_types=1);
/**
 * Employee detail view.
 * Variables: $employee, $csrfToken, $appConfig
 */
require ROOT_PATH . '/app/Views/layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="/employees" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-bold">Employee Details</h5>
    <div class="ms-auto d-flex gap-2">
        <a href="/employees/<?= (int) $employee['id'] ?>/edit" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <button type="button"
                class="btn btn-sm btn-outline-danger"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal"
                data-emp-id="<?= (int) $employee['id'] ?>"
                data-emp-name="<?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name'], ENT_QUOTES) ?>">
            <i class="bi bi-trash me-1"></i> Delete
        </button>
    </div>
</div>

<div class="row g-3">
    <!-- ── Main details card ──────────────────────────────────────── -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-primary"></i> Personal Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Full Name</p>
                        <p class="mb-0 fw-semibold fs-5">
                            <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name'], ENT_QUOTES) ?>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Status</p>
                        <span class="badge rounded-pill px-3 py-1 <?= $employee['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?> fs-6">
                            <?= ucfirst(htmlspecialchars($employee['status'], ENT_QUOTES)) ?>
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Email</p>
                        <a href="mailto:<?= htmlspecialchars($employee['email'], ENT_QUOTES) ?>" class="text-decoration-none">
                            <?= htmlspecialchars($employee['email'], ENT_QUOTES) ?>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Phone</p>
                        <p class="mb-0">
                            <?php if (!empty($employee['phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($employee['phone'], ENT_QUOTES) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($employee['phone'], ENT_QUOTES) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-briefcase text-primary"></i> Employment Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Department</p>
                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($employee['department'], ENT_QUOTES) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Position</p>
                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($employee['position'], ENT_QUOTES) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Salary</p>
                        <p class="mb-0 fw-semibold fs-5 text-success">
                            $<?= number_format((float) $employee['salary'], 2) ?>
                            <span class="text-muted fw-normal fs-6">/ year</span>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" style="font-size:.7rem;letter-spacing:.06em;">Date Joined</p>
                        <p class="mb-0">
                            <?= htmlspecialchars(date('F j, Y', strtotime($employee['date_joined'])), ENT_QUOTES) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Sidebar ─────────────────────────────────────────────────── -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-secondary"></i> Record Info
            </div>
            <div class="card-body small">
                <div class="row g-2">
                    <div class="col-12">
                        <p class="text-muted mb-1" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Employee ID</p>
                        <p class="mb-2 fw-semibold">#<?= (int) $employee['id'] ?></p>
                    </div>
                    <div class="col-12">
                        <p class="text-muted mb-1" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Created At</p>
                        <p class="mb-2"><?= htmlspecialchars($employee['created_at'] ?? '—', ENT_QUOTES) ?></p>
                    </div>
                    <div class="col-12">
                        <p class="text-muted mb-1" style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Last Updated</p>
                        <p class="mb-0"><?= htmlspecialchars($employee['updated_at'] ?? '—', ENT_QUOTES) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-gear text-secondary"></i> Actions
            </div>
            <div class="card-body d-grid gap-2">
                <a href="/employees/<?= (int) $employee['id'] ?>/edit" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit Employee
                </a>
                <a href="/employees" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
                <hr class="my-1">
                <button type="button"
                        class="btn btn-outline-danger btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-emp-id="<?= (int) $employee['id'] ?>"
                        data-emp-name="<?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name'], ENT_QUOTES) ?>">
                    <i class="bi bi-trash me-1"></i> Delete Employee
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to permanently delete
                <strong id="deleteEmpName"></strong>?
                This action cannot be undone.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/layout/footer.php'; ?>
