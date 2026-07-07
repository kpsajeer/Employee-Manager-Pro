<?php declare(strict_types=1);
/**
 * Employee list view.
 * Variables: $employees, $total, $totalPages, $params, $csrfToken, $appConfig
 */
require ROOT_PATH . '/app/Views/layout/header.php';

$currentPage = (int) ($params['page'] ?? 1);
$search      = htmlspecialchars($params['search']     ?? '', ENT_QUOTES);
$dept        = htmlspecialchars($params['department'] ?? '', ENT_QUOTES);
$sort        = $params['sort']  ?? 'id';
$order       = $params['order'] ?? 'ASC';

/**
 * Build a query string preserving all current params, overriding specified keys.
 * @param array<string,string> $overrides
 */
function buildQuery(array $overrides, array $params): string
{
    $q = array_merge([
        'search'     => $params['search']     ?? '',
        'department' => $params['department'] ?? '',
        'sort'       => $params['sort']       ?? 'id',
        'order'      => $params['order']      ?? 'ASC',
        'page'       => $params['page']       ?? '1',
    ], $overrides);
    return '?' . http_build_query(array_filter($q, fn($v) => $v !== ''));
}

/**
 * Return the sort link URL for a given column.
 */
function sortLink(string $col, array $params): string
{
    $currentSort  = $params['sort']  ?? 'id';
    $currentOrder = $params['order'] ?? 'ASC';
    $newOrder     = ($currentSort === $col && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    return '/employees' . buildQuery(['sort' => $col, 'order' => $newOrder, 'page' => '1'], $params);
}

/**
 * Return CSS class for sort arrow on a column.
 */
function sortClass(string $col, array $params): string
{
    if (($params['sort'] ?? '') !== $col) return '';
    return ($params['order'] ?? 'ASC') === 'ASC' ? 'sort-asc' : 'sort-desc';
}
?>

<!-- ── Toolbar ──────────────────────────────────────────────────────── -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="/employees" class="row g-2 align-items-end" id="filterForm">
            <div class="col-sm-5 col-md-4">
                <label class="form-label small mb-1 text-muted fw-semibold">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Name or email…" value="<?= $search ?>">
                </div>
            </div>

            <div class="col-sm-4 col-md-3">
                <label class="form-label small mb-1 text-muted fw-semibold">Department</label>
                <select name="department" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php foreach ($appConfig['departments'] as $d): ?>
                        <option value="<?= htmlspecialchars($d, ENT_QUOTES) ?>"
                            <?= ($dept === htmlspecialchars($d, ENT_QUOTES) ? 'selected' : '') ?>>
                            <?= htmlspecialchars($d, ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="/employees" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>

            <div class="col-auto ms-auto">
                <a href="/employees/create" class="btn btn-success btn-sm px-3">
                    <i class="bi bi-person-plus-fill"></i> Add Employee
                </a>
                <a href="/employees/export<?= buildQuery([], $params) ?>"
                   class="btn btn-outline-secondary btn-sm px-3 ms-1">
                    <i class="bi bi-filetype-csv"></i> Export
                </a>
            </div>

            <!-- Preserve sort in filter submissions -->
            <input type="hidden" name="sort"  value="<?= htmlspecialchars($sort,  ENT_QUOTES) ?>">
            <input type="hidden" name="order" value="<?= htmlspecialchars($order, ENT_QUOTES) ?>">
        </form>
    </div>
</div>

<!-- ── Results info ─────────────────────────────────────────────────── -->
<div class="d-flex align-items-center justify-content-between mb-2">
    <p class="text-muted small mb-0">
        <?php if ($total === 0): ?>
            No employees found.
        <?php else: ?>
            Showing
            <strong><?= min(($currentPage - 1) * $appConfig['pagination_limit'] + 1, $total) ?></strong>
            –
            <strong><?= min($currentPage * $appConfig['pagination_limit'], $total) ?></strong>
            of <strong><?= $total ?></strong> employee<?= $total !== 1 ? 's' : '' ?>
            <?= $search !== '' ? "matching <em>\"$search\"</em>" : '' ?>
        <?php endif; ?>
    </p>
</div>

<!-- ── Table ────────────────────────────────────────────────────────── -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><a href="<?= sortLink('id', $params) ?>" class="<?= sortClass('id', $params) ?>">#</a></th>
                        <th><a href="<?= sortLink('first_name', $params) ?>" class="<?= sortClass('first_name', $params) ?>">Name</a></th>
                        <th><a href="<?= sortLink('email', $params) ?>" class="<?= sortClass('email', $params) ?>">Email</a></th>
                        <th><a href="<?= sortLink('department', $params) ?>" class="<?= sortClass('department', $params) ?>">Department</a></th>
                        <th><a href="<?= sortLink('position', $params) ?>" class="<?= sortClass('position', $params) ?>">Position</a></th>
                        <th><a href="<?= sortLink('salary', $params) ?>" class="<?= sortClass('salary', $params) ?>">Salary</a></th>
                        <th><a href="<?= sortLink('date_joined', $params) ?>" class="<?= sortClass('date_joined', $params) ?>">Joined</a></th>
                        <th><a href="<?= sortLink('status', $params) ?>" class="<?= sortClass('status', $params) ?>">Status</a></th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No employees found.
                                <?php if ($search !== '' || $dept !== ''): ?>
                                    <a href="/employees">Clear filters</a>
                                <?php else: ?>
                                    <a href="/employees/create">Add the first one</a>.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td class="text-muted"><?= (int) $emp['id'] ?></td>
                                <td>
                                    <a href="/employees/<?= (int) $emp['id'] ?>" class="fw-semibold text-decoration-none">
                                        <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'], ENT_QUOTES) ?>
                                    </a>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($emp['email'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($emp['department'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($emp['position'], ENT_QUOTES) ?></td>
                                <td>$<?= number_format((float) $emp['salary'], 2) ?></td>
                                <td><?= htmlspecialchars($emp['date_joined'], ENT_QUOTES) ?></td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-1 <?= $emp['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                        <?= ucfirst(htmlspecialchars($emp['status'], ENT_QUOTES)) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/employees/<?= (int) $emp['id'] ?>"
                                           class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/employees/<?= (int) $emp['id'] ?>/edit"
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                title="Delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-emp-id="<?= (int) $emp['id'] ?>"
                                                data-emp-name="<?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'], ENT_QUOTES) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination footer -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-center py-3">
            <nav aria-label="Pagination">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="/employees<?= buildQuery(['page' => (string)($currentPage - 1)], $params) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>

                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php
                        // Show first, last, current ± 2, and ellipses
                        $show = ($p === 1 || $p === $totalPages || abs($p - $currentPage) <= 2);
                        $ellipsisShown = false;
                        if (!$show) {
                            if (($p === 2 && $currentPage > 4) || ($p === $totalPages - 1 && $currentPage < $totalPages - 3)) {
                                echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                            }
                            continue;
                        }
                        ?>
                        <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                            <a class="page-link"
                               href="/employees<?= buildQuery(['page' => (string)$p], $params) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="/employees<?= buildQuery(['page' => (string)($currentPage + 1)], $params) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- ── Delete confirmation modal ────────────────────────────────────── -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deleteModalLabel">
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
