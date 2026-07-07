<?php declare(strict_types=1);
/**
 * Create employee form.
 * Variables: $employee (pre-filled on validation failure), $errors, $csrfToken, $appConfig
 */
require ROOT_PATH . '/app/Views/layout/header.php';

/**
 * Helper: return the field's old value (for repopulation after validation failure).
 */
function old(string $field, array $employee, string $default = ''): string
{
    return htmlspecialchars((string) ($employee[$field] ?? $default), ENT_QUOTES);
}

/**
 * Helper: return Bootstrap error class if a field has an error.
 */
function fieldClass(string $field, array $errors): string
{
    return isset($errors[$field]) ? ' is-invalid' : '';
}
?>

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="/employees" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-bold">Add New Employee</h5>
</div>

<form method="POST" action="/employees/store" novalidate>
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
                               class="form-control<?= fieldClass('first_name', $errors) ?>"
                               value="<?= old('first_name', $employee) ?>"
                               placeholder="Jane">
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['first_name'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" maxlength="100"
                               class="form-control<?= fieldClass('last_name', $errors) ?>"
                               value="<?= old('last_name', $employee) ?>"
                               placeholder="Smith">
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['last_name'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control<?= fieldClass('email', $errors) ?>"
                               value="<?= old('email', $employee) ?>"
                               placeholder="jane.smith@example.com">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="tel" name="phone"
                               class="form-control<?= fieldClass('phone', $errors) ?>"
                               value="<?= old('phone', $employee) ?>"
                               placeholder="+1 555-000-0000">
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['phone'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- ── Employment Info ──────────────────────────────── -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-briefcase me-1 text-primary"></i> Employment Details
                </div>
                <div class="card-body row g-3">

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                        <select name="department" class="form-select<?= fieldClass('department', $errors) ?>">
                            <option value="">Select department…</option>
                            <?php foreach ($appConfig['departments'] as $dept): ?>
                                <option value="<?= htmlspecialchars($dept, ENT_QUOTES) ?>"
                                    <?= (old('department', $employee) === htmlspecialchars($dept, ENT_QUOTES) ? 'selected' : '') ?>>
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
                               class="form-control<?= fieldClass('position', $errors) ?>"
                               value="<?= old('position', $employee) ?>"
                               placeholder="Software Engineer">
                        <?php if (isset($errors['position'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['position'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Salary (USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="salary" step="0.01" min="0"
                                   class="form-control<?= fieldClass('salary', $errors) ?>"
                                   value="<?= old('salary', $employee, '0.00') ?>"
                                   placeholder="50000.00">
                        </div>
                        <?php if (isset($errors['salary'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['salary'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Date Joined <span class="text-danger">*</span></label>
                        <input type="date" name="date_joined"
                               class="form-control<?= fieldClass('date_joined', $errors) ?>"
                               value="<?= old('date_joined', $employee, date('Y-m-d')) ?>">
                        <?php if (isset($errors['date_joined'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['date_joined'], ENT_QUOTES) ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── Sidebar: status + submit ─────────────────────────── -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-toggle-on me-1 text-primary"></i> Status
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="status" id="statusActive"
                               value="active" <?= (old('status', $employee, 'active') === 'active' ? 'checked' : '') ?>>
                        <label class="form-check-label" for="statusActive">
                            <span class="badge badge-active px-3 py-1 rounded-pill">Active</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="statusInactive"
                               value="inactive" <?= (old('status', $employee) === 'inactive' ? 'checked' : '') ?>>
                        <label class="form-check-label" for="statusInactive">
                            <span class="badge badge-inactive px-3 py-1 rounded-pill">Inactive</span>
                        </label>
                    </div>
                    <?php if (isset($errors['status'])): ?>
                        <div class="text-danger small mt-1"><?= htmlspecialchars($errors['status'], ENT_QUOTES) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill me-1"></i> Create Employee
                    </button>
                    <a href="/employees" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require ROOT_PATH . '/app/Views/layout/footer.php'; ?>
