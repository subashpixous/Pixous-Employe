<?php
$pageTitle = 'Dashboard';
$pendingLeaveCount = $leaveCounts['pending'] ?? 0;
require __DIR__ . '/../layouts/header.php';

$barColors = ['#0a1628','#d4a532','#3b82f6','#22c55e','#8892a0','#ef4444','#6b2130','#1a3950','#501a3a'];
$maxDept = max(array_column($deptCounts, 'cnt') ?: [1]);
?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft"><i class="bi bi-people-fill"></i></div>
            <div>
                <p class="stat-value"><?= $empCounts['total'] ?></p>
                <p class="stat-label">Total Employees</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="bi bi-person-check-fill"></i></div>
            <div>
                <p class="stat-value"><?= $empCounts['active'] ?></p>
                <p class="stat-label">Active Staff</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft"><i class="bi bi-calendar2-check"></i></div>
            <div>
                <p class="stat-value"><?= $leaveCounts['pending'] ?></p>
                <p class="stat-label">Pending Leaves</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon bg-gold-soft"><i class="bi bi-cash-coin"></i></div>
            <div>
                <p class="stat-value"><?= formatCurrency($totalSalary) ?></p>
                <p class="stat-label">Monthly Payroll</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h6>Department Headcount</h6></div>
            <div class="card-body">
                <div class="chart-bars">
                    <?php foreach (array_slice($deptCounts, 0, 7) as $i => $dept): ?>
                    <div class="chart-col">
                        <div class="bar-value"><?= (int)$dept['cnt'] ?></div>
                        <div class="bar" style="height:<?= round(($dept['cnt']/$maxDept)*100) ?>%;background:<?= $barColors[$i % count($barColors)] ?>"></div>
                        <div class="bar-label"><?= e(substr($dept['name'], 0, 6)) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h6>Task Overview</h6></div>
            <div class="card-body">
                <?php
                $taskItems = [
                    ['Completed',   $taskCounts['Completed'],    $taskCounts['total'], 'success'],
                    ['In Progress', $taskCounts['In Progress'],  $taskCounts['total'], 'gold'],
                    ['Pending',     $taskCounts['Pending'],      $taskCounts['total'], 'danger'],
                ];
                foreach ($taskItems as [$label, $val, $total, $color]):
                    $pct = $total > 0 ? round(($val/$total)*100) : 0;
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size:13px;font-weight:600"><?= $label ?></span>
                        <span class="text-navy fw-700" style="font-size:13px"><?= $val ?>/<?= $total ?></span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-<?= $color ?>" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Leaves -->
<div class="card">
    <div class="card-header">
        <h6>Recent Leave Requests</h6>
        <a href="<?= url('leaves') ?>" class="btn btn-outline-secondary btn-sm">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Employee</th><th>Type</th><th>Duration</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($recentLeaves['data'], 0, 5) as $lv): ?>
                <tr>
                    <td>
                        <div class="emp-cell">
                            <div class="avatar-circle" style="background:<?= avatarColor($lv['employee_name']) ?>;width:32px;height:32px;font-size:11px">
                                <?= initials($lv['employee_name']) ?>
                            </div>
                            <span class="name"><?= e($lv['employee_name']) ?></span>
                        </div>
                    </td>
                    <td><?= e($lv['leave_type']) ?></td>
                    <td><?= formatDate($lv['date_from']) ?> → <?= formatDate($lv['date_to']) ?> <small class="text-muted">(<?= $lv['days'] ?>d)</small></td>
                    <td><span class="badge-status badge-<?= e($lv['status']) ?>"><?= ucfirst(e($lv['status'])) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentLeaves['data'])): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No leave requests yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
