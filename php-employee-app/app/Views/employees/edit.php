<?php declare(strict_types=1);
/**
 * Edit employee form.
 * Variables: $employee, $errors, $csrfToken, $appConfig
 */
require ROOT_PATH . '/app/Views/layout/header.php';

function oldEdit(string $field, array $employee, string $default = ''): string
{
    return htmlspecialchars((string) ($employee[$field] ?? $default), ENT_QUOTES);
}
function fieldClassEdit(string $field, array $errors): string
{
    return isset($errors[$field]) ? ' is-invalid' : '';
}
?>

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="/employees/<?= (int) $employee['id'] ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-bold">
        Edit — <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name'], ENT_QUOTES) ?>
    </h5>
</div>

<form method="POST" action="/employees/<?= (int) $employee['id'] ?>/update" novalidate>
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">

    <div class="row g-3">
        <!-- ── Personal Info ──────────────────────────────────────── -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-person-badge me-1 text-primary"></i> Personal Information
                </div>
                <div class="card-body row g-3">

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" maxlength="100"
                               class="form-control<?= fieldClassEdit('first_name', $errors) ?>"
                               value="<?= oldEdit('first_name', $employee) ?>">
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['first_name'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" maxlength="100"
                               class="form-control<?= fieldClassEdit('last_name', $errors) ?>"
                               value="<?= oldEdit('last_name', $employee) ?>">
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['last_name'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control<?= fieldClassEdit('email', $errors) ?>"
                               value="<?= oldEdit('email', $employee) ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="tel" name="phone"
                               class="form-control<?= fieldClassEdit('phone', $errors) ?>"
                               value="<?= oldEdit('phone', $employee) ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['phone'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- ── Employment Details ──────────────────────────── -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-briefcase me-1 text-primary"></i> Employment Details
                </div>
                <div class="card-body row g-3">

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                        <select name="department" class="form-select<?= fieldClassEdit('department', $errors) ?>">
                            <option value="">Select department…</option>
                            <?php foreach ($appConfig['departments'] as $dept): ?>
                                <option value="<?= htmlspecialchars($dept, ENT_QUOTES) ?>"
                                    <?= (oldEdit('department', $employee) === htmlspecialchars($dept, ENT_QUOTES) ? 'selected' : '') ?>>
                                    <?= htmlspecialchars($dept, ENT_QUOTES) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['department'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['department'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Position <span class="text-danger">*</span></label>
                        <input type="text" name="position"
                               class="form-control<?= fieldClassEdit('position', $errors) ?>"
                               value="<?= oldEdit('position', $employee) ?>">
                        <?php if (isset($errors['position'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['position'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Salary (USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="salary" step="0.01" min="0"
                                   class="form-control<?= fieldClassEdit('salary', $errors) ?>"
                                   value="<?= oldEdit('salary', $employee, '0.00') ?>">
                        </div>
                        <?php if (isset($errors['salary'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['salary'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Date Joined <span class="text-danger">*</span></label>
                        <input type="date" name="date_joined"
                               class="form-control<?= fieldClassEdit('date_joined', $errors) ?>"
                               value="<?= oldEdit('date_joined', $employee) ?>">
                        <?php if (isset($errors['date_joined'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['date_joined'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── Sidebar ──────────────────────────────────────────── -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-toggle-on me-1 text-primary"></i> Status
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="status" id="statusActive"
                               value="active" <?= (oldEdit('status', $employee) === 'active' ? 'checked' : '') ?>>
                        <label class="form-check-label" for="statusActive">
                            <span class="badge badge-active px-3 py-1 rounded-pill">Active</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="statusInactive"
                               value="inactive" <?= (oldEdit('status', $employee) === 'inactive' ? 'checked' : '') ?>>
                        <label class="form-check-label" for="statusInactive">
                            <span class="badge badge-inactive px-3 py-1 rounded-pill">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-clock-history me-1 text-secondary"></i> Record Info
                </div>
                <div class="card-body small text-muted">
                    <div class="mb-1"><strong>ID:</strong> <?= (int) $employee['id'] ?></div>
                    <div class="mb-1">
                        <strong>Created:</strong><br>
                        <?= htmlspecialchars($employee['created_at'] ?? '—', ENT_QUOTES) ?>
                    </div>
                    <div>
                        <strong>Updated:</strong><br>
                        <?= htmlspecialchars($employee['updated_at'] ?? '—', ENT_QUOTES) ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                    <a href="/employees/<?= (int) $employee['id'] ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require ROOT_PATH . '/app/Views/layout/footer.php'; ?>
