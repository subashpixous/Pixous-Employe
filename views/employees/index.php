<?php
$pageTitle = 'Employees';
$leaveModel = new LeaveRequest();
$pendingLeaveCount = $leaveModel->countByStatus()['pending'];
require __DIR__ . '/../layouts/header.php';
$sf = $_GET['status'] ?? '';
$df = $_GET['department_id'] ?? '';
$sq = $_GET['search'] ?? '';
?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft"><i class="bi bi-people-fill"></i></div>
            <div><p class="stat-value"><?= $counts['total'] ?></p><p class="stat-label">Total</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="bi bi-person-check"></i></div>
            <div><p class="stat-value"><?= $counts['active'] ?></p><p class="stat-label">Active</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-danger-soft"><i class="bi bi-person-x"></i></div>
            <div><p class="stat-value"><?= $counts['inactive'] ?></p><p class="stat-label">Inactive</p></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6><i class="bi bi-people me-2"></i>Employee Directory (<?= $pagination['total'] ?>)</h6>
        <div class="d-flex gap-2 flex-wrap">
            <form class="d-flex gap-2 flex-wrap" method="GET">
                <input type="hidden" name="page" value="employees">
                <select name="status" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" <?= $sf==='active'?'selected':'' ?>>Active</option>
                    <option value="inactive" <?= $sf==='inactive'?'selected':'' ?>>Inactive</option>
                </select>
                <select name="department_id" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $df==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="input-group input-group-sm" style="width:200px">
                    <input name="search" class="form-control" placeholder="Search..." value="<?= e($sq) ?>">
                    <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <a href="<?= url('employees/create') ?>" class="btn btn-gold btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Employee
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Employee</th><th>ID</th><th>Department</th><th>Designation</th>
                        <th>Mobile</th><th>Status</th><th style="width:180px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($employees as $emp): ?>
                <tr>
                    <td>
                        <div class="emp-cell">
                            <div class="avatar-circle" style="background:<?= avatarColor($emp['name']) ?>">
                                <?= initials($emp['name']) ?>
                            </div>
                            <div>
                                <div class="name"><?= e($emp['name']) ?></div>
                                <div class="sub"><?= e($emp['email'] ?: '—') ?></div>
                            </div>
                        </div>
                    </td>
                    <td><code style="font-size:12px;color:var(--gray-400)"><?= e($emp['emp_code']) ?></code></td>
                    <td><?= e($emp['department_name'] ?? '—') ?></td>
                    <td><?= e($emp['designation_name'] ?? '—') ?></td>
                    <td><?= e($emp['mobile']) ?></td>
                    <td><span class="badge-status badge-<?= e($emp['status']) ?>"><?= ucfirst(e($emp['status'])) ?></span></td>
                    <td>
                        <div class="action-btns">
                            <a href="<?= url("employees/view&id={$emp['id']}") ?>" class="btn btn-outline-secondary btn-sm" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= url("employees/edit&id={$emp['id']}") ?>" class="btn btn-outline-secondary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>

                            <?php
                            $newStatus = $emp['status'] === 'active' ? 'inactive' : 'active';
                            $toggleLabel = $emp['status'] === 'active' ? 'Deactivate' : 'Reactivate';
                            $toggleIcon  = $emp['status'] === 'active' ? 'bi-person-x' : 'bi-person-check';
                            ?>
                            <form id="toggle-<?= $emp['id'] ?>" method="POST" action="<?= url('employees/toggle-status') ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                <input type="hidden" name="status" value="<?= $newStatus ?>">
                                <button type="button" class="btn btn-outline-secondary btn-sm" title="<?= $toggleLabel ?>"
                                        onclick="confirmToggle('toggle-<?= $emp['id'] ?>','<?= $toggleLabel ?>','<?= e($emp['name']) ?>')">
                                    <i class="bi <?= $toggleIcon ?>"></i>
                                </button>
                            </form>

                            <form id="del-emp-<?= $emp['id'] ?>" method="POST" action="<?= url('employees/delete') ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                        onclick="confirmDelete('del-emp-<?= $emp['id'] ?>','<?= e($emp['name']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($employees)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:28px;opacity:0.3"></i><br>No employees found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="card-footer bg-white border-top d-flex justify-content-center py-3">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                <li class="page-item <?= $p === $pagination['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url("employees&pg={$p}&status={$sf}&department_id={$df}&search=" . urlencode($sq)) ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
