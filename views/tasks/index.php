<?php
$pageTitle = 'Task Monitor';
$leaveModel2 = new LeaveRequest();
$pendingLeaveCount = $leaveModel2->countByStatus()['pending'];
require __DIR__ . '/../layouts/header.php';
$sf = $_GET['status'] ?? '';
$pf = $_GET['priority'] ?? '';
?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card"><div class="stat-icon bg-warning-soft"><i class="bi bi-hourglass-split"></i></div>
        <div><p class="stat-value"><?= $counts['Pending'] ?></p><p class="stat-label">Pending</p></div></div>
    </div>
    <div class="col-md-4">
        <div class="stat-card"><div class="stat-icon bg-primary-soft"><i class="bi bi-arrow-repeat"></i></div>
        <div><p class="stat-value"><?= $counts['In Progress'] ?></p><p class="stat-label">In Progress</p></div></div>
    </div>
    <div class="col-md-4">
        <div class="stat-card"><div class="stat-icon bg-success-soft"><i class="bi bi-check-circle-fill"></i></div>
        <div><p class="stat-value"><?= $counts['Completed'] ?></p><p class="stat-label">Completed</p></div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6><i class="bi bi-kanban me-2"></i>Task Board (<?= $pagination['total'] ?>)</h6>
        <div class="d-flex gap-2">
            <form class="d-flex gap-2" method="GET">
                <input type="hidden" name="page" value="tasks">
                <select name="status" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <?php foreach (['Pending','In Progress','Completed'] as $s): ?>
                    <option <?= $sf===$s?'selected':'' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="priority" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <option value="">All Priority</option>
                    <?php foreach (['High','Medium','Low'] as $pr): ?>
                    <option <?= $pf===$pr?'selected':'' ?>><?= $pr ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="resetTaskForm()">
                <i class="bi bi-plus-lg me-1"></i> New Task
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Task</th><th>Assigned To</th><th>Priority</th><th>Deadline</th><th>Progress</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($tasks as $t):
                    $pctClass = $t['progress'] >= 100 ? 'success' : ($t['progress'] >= 40 ? 'gold' : 'danger');
                    $statusBadge = $t['status']==='Completed' ? 'approved' : ($t['status']==='In Progress' ? 'pending' : 'inactive');
                ?>
                <tr>
                    <td>
                        <div class="name" style="font-weight:600;color:var(--navy-text)"><?= e($t['title']) ?></div>
                        <div class="sub" style="font-size:11px;color:var(--gray-400);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <?= e($t['description'] ?? '') ?>
                        </div>
                    </td>
                    <td>
                        <div class="emp-cell">
                            <div class="avatar-circle" style="background:<?= avatarColor($t['assignee_name']) ?>;width:28px;height:28px;font-size:10px">
                                <?= initials($t['assignee_name']) ?>
                            </div>
                            <span><?= e($t['assignee_name']) ?></span>
                        </div>
                    </td>
                    <td><span class="badge-status badge-<?= strtolower($t['priority']) ?>"><?= e($t['priority']) ?></span></td>
                    <td><?= formatDate($t['deadline']) ?></td>
                    <td style="min-width:130px">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1">
                                <div class="progress-bar progress-bar-<?= $pctClass ?>" style="width:<?= $t['progress'] ?>%"></div>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:var(--navy-text);min-width:30px"><?= $t['progress'] ?>%</span>
                        </div>
                    </td>
                    <td><span class="badge-status badge-<?= $statusBadge ?>"><?= e($t['status']) ?></span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-outline-secondary btn-sm" title="Edit"
                                    onclick='editTask(<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form id="del-task-<?= $t['id'] ?>" method="POST" action="<?= url('tasks/delete') ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                        onclick="confirmDelete('del-task-<?= $t['id'] ?>','<?= e($t['title']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($tasks)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No tasks found.</td></tr>
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
                <a class="page-link" href="<?= url("tasks&pg={$p}&status={$sf}&priority={$pf}") ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>

<!-- Task Modal (Create/Edit) -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-display" id="taskModalTitle">New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="taskForm" action="<?= url('tasks/store') ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" id="taskId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Task Title *</label>
                        <input name="title" id="taskTitle" class="form-control" required placeholder="Enter task title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="taskDesc" class="form-control" rows="3" placeholder="Task details..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign To *</label>
                        <select name="assigned_to" id="taskAssign" class="form-select" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($activeEmps as $ae): ?>
                            <option value="<?= $ae['id'] ?>"><?= e($ae['name']) ?> (<?= e($ae['emp_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" id="taskPriority" class="form-select">
                                <option>High</option><option selected>Medium</option><option>Low</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="taskStatus" class="form-select">
                                <option>Pending</option><option>In Progress</option><option>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Deadline *</label>
                            <input type="date" name="deadline" id="taskDeadline" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Progress: <strong id="progressLabel">0</strong>%</label>
                            <input type="range" name="progress" id="taskProgress" class="form-range mt-1" min="0" max="100" step="5" value="0"
                                   oninput="document.getElementById('progressLabel').textContent=this.value"
                                   style="accent-color:var(--gold)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold" id="taskSubmitBtn">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetTaskForm() {
    document.getElementById('taskModalTitle').textContent = 'New Task';
    document.getElementById('taskSubmitBtn').textContent = 'Create Task';
    document.getElementById('taskForm').action = '<?= url("tasks/store") ?>';
    document.getElementById('taskId').value = '';
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDesc').value = '';
    document.getElementById('taskAssign').value = '';
    document.getElementById('taskPriority').value = 'Medium';
    document.getElementById('taskStatus').value = 'Pending';
    document.getElementById('taskDeadline').value = '';
    document.getElementById('taskProgress').value = 0;
    document.getElementById('progressLabel').textContent = '0';
}

function editTask(t) {
    document.getElementById('taskModalTitle').textContent = 'Edit Task';
    document.getElementById('taskSubmitBtn').textContent = 'Update Task';
    document.getElementById('taskForm').action = '<?= url("tasks/update") ?>';
    document.getElementById('taskId').value = t.id;
    document.getElementById('taskTitle').value = t.title;
    document.getElementById('taskDesc').value = t.description || '';
    document.getElementById('taskAssign').value = t.assigned_to;
    document.getElementById('taskPriority').value = t.priority;
    document.getElementById('taskStatus').value = t.status;
    document.getElementById('taskDeadline').value = t.deadline;
    document.getElementById('taskProgress').value = t.progress;
    document.getElementById('progressLabel').textContent = t.progress;
    new bootstrap.Modal(document.getElementById('taskModal')).show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
