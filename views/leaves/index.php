<?php
$pageTitle = 'Leave Management';
$pendingLeaveCount = $counts['pending'];
require __DIR__ . '/../layouts/header.php';
$sf = $_GET['status'] ?? '';
$sq = $_GET['search'] ?? '';
?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="stat-icon bg-primary-soft"><i class="bi bi-calendar2-check"></i></div>
        <div><p class="stat-value"><?= $counts['total'] ?></p><p class="stat-label">Total</p></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="stat-icon bg-warning-soft"><i class="bi bi-hourglass-split"></i></div>
        <div><p class="stat-value"><?= $counts['pending'] ?></p><p class="stat-label">Pending</p></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="stat-icon bg-success-soft"><i class="bi bi-check-circle"></i></div>
        <div><p class="stat-value"><?= $counts['approved'] ?></p><p class="stat-label">Approved</p></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="stat-icon bg-danger-soft"><i class="bi bi-x-circle"></i></div>
        <div><p class="stat-value"><?= $counts['rejected'] ?></p><p class="stat-label">Rejected</p></div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6><i class="bi bi-calendar2-week me-2"></i>Leave Requests</h6>
        <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#leaveModal">
            <i class="bi bi-plus-lg me-1"></i> New Request
        </button>
    </div>
    <div class="card-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs nav-tabs-filter mb-3">
            <li class="nav-item"><a class="nav-link <?= empty($sf)?'active':'' ?>" href="<?= url('leaves') ?>">All (<?= $counts['total'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= $sf==='pending'?'active':'' ?>" href="<?= url('leaves&status=pending') ?>">Pending (<?= $counts['pending'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= $sf==='approved'?'active':'' ?>" href="<?= url('leaves&status=approved') ?>">Approved (<?= $counts['approved'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= $sf==='rejected'?'active':'' ?>" href="<?= url('leaves&status=rejected') ?>">Rejected (<?= $counts['rejected'] ?>)</a></li>
        </ul>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($leaves as $lv): ?>
                <tr>
                    <td>
                        <div class="emp-cell">
                            <div class="avatar-circle" style="background:<?= avatarColor($lv['employee_name']) ?>;width:30px;height:30px;font-size:10px">
                                <?= initials($lv['employee_name']) ?>
                            </div>
                            <div>
                                <span class="name"><?= e($lv['employee_name']) ?></span>
                                <div class="sub"><?= e($lv['emp_code']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= e($lv['leave_type']) ?></td>
                    <td><?= formatDate($lv['date_from']) ?></td>
                    <td><?= formatDate($lv['date_to']) ?></td>
                    <td><strong><?= $lv['days'] ?></strong></td>
                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= e($lv['reason']) ?>">
                        <?= e($lv['reason']) ?>
                    </td>
                    <td><span class="badge-status badge-<?= e($lv['status']) ?>"><?= ucfirst(e($lv['status'])) ?></span></td>
                    <td>
                        <?php if ($lv['status'] === 'pending'): ?>
                        <div class="action-btns">
                            <form id="approve-<?= $lv['id'] ?>" method="POST" action="<?= url('leaves/approve') ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $lv['id'] ?>">
                                <input type="hidden" name="action" value="approved">
                                <button type="button" class="btn btn-success btn-sm" title="Approve"
                                        onclick="confirmLeaveAction('approve-<?= $lv['id'] ?>','approved')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <form id="reject-<?= $lv['id'] ?>" method="POST" action="<?= url('leaves/approve') ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $lv['id'] ?>">
                                <input type="hidden" name="action" value="rejected">
                                <button type="button" class="btn btn-danger btn-sm" title="Reject"
                                        onclick="confirmLeaveAction('reject-<?= $lv['id'] ?>','rejected')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($leaves)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No leave requests found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="card-footer bg-white border-top d-flex justify-content-center py-3">
        <nav><ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
            <li class="page-item <?= $p===$pagination['page']?'active':'' ?>">
                <a class="page-link" href="<?= url("leaves&pg={$p}&status={$sf}") ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>

<!-- New Leave Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-display">New Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('leaves/store') ?>">
                <?= csrfField() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee *</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($activeEmps as $ae): ?>
                            <option value="<?= $ae['id'] ?>"><?= e($ae['name']) ?> (<?= e($ae['emp_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave Type *</label>
                        <select name="leave_type_id" class="form-select" required>
                            <?php foreach ($leaveTypes as $lt): ?>
                            <option value="<?= $lt['id'] ?>"><?= e($lt['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">From *</label>
                            <input type="date" name="date_from" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">To *</label>
                            <input type="date" name="date_to" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason *</label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Enter reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
