<?php
$pageTitle = 'Payroll';
$leaveModel2 = new LeaveRequest();
$pendingLeaveCount = $leaveModel2->countByStatus()['pending'];
require __DIR__ . '/../layouts/header.php';

$months = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
$curMonth = (int)($_GET['month'] ?? date('n'));
$curYear  = (int)($_GET['year']  ?? date('Y'));
?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card"><div class="stat-icon bg-primary-soft"><i class="bi bi-people-fill"></i></div>
        <div><p class="stat-value"><?= (int)$summary['count'] ?></p><p class="stat-label">Processed</p></div></div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card"><div class="stat-icon bg-gold-soft"><i class="bi bi-cash-coin"></i></div>
        <div><p class="stat-value"><?= formatCurrency((float)$summary['total_gross']) ?></p><p class="stat-label">Total Gross</p></div></div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card"><div class="stat-icon bg-danger-soft"><i class="bi bi-dash-circle"></i></div>
        <div><p class="stat-value"><?= formatCurrency((float)$summary['total_pf'] + (float)$summary['total_esi'] + (float)$summary['total_pt']) ?></p><p class="stat-label">Total Deductions</p></div></div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card"><div class="stat-icon bg-success-soft"><i class="bi bi-wallet2"></i></div>
        <div><p class="stat-value"><?= formatCurrency((float)$summary['total_net']) ?></p><p class="stat-label">Net Payable</p></div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6><i class="bi bi-cash-coin me-2"></i>Payroll — <?= $months[$curMonth] ?> <?= $curYear ?></h6>
        <div class="d-flex gap-2 flex-wrap">
            <form class="d-flex gap-2" method="GET">
                <input type="hidden" name="page" value="payroll">
                <select name="month" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <?php for ($m=1;$m<=12;$m++): ?>
                    <option value="<?= $m ?>" <?= $m===$curMonth?'selected':'' ?>><?= $months[$m] ?></option>
                    <?php endfor; ?>
                </select>
                <select name="year" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <?php for ($y=2024;$y<=2028;$y++): ?>
                    <option value="<?= $y ?>" <?= $y===$curYear?'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </form>

            <?php if (!$hasData): ?>
            <form method="POST" action="<?= url('payroll/generate') ?>" id="generateForm">
                <?= csrfField() ?>
                <input type="hidden" name="month" value="<?= $curMonth ?>">
                <input type="hidden" name="year" value="<?= $curYear ?>">
                <button type="button" class="btn btn-gold btn-sm" onclick="
                    Swal.fire({
                        title:'Process Payroll?',
                        text:'Generate payroll for <?= $months[$curMonth] ?> <?= $curYear ?>?',
                        icon:'question',
                        showCancelButton:true,
                        confirmButtonColor:'#d4a532',
                        confirmButtonText:'Process',
                    }).then(r=>{if(r.isConfirmed)document.getElementById('generateForm').submit()})
                ">
                    <i class="bi bi-gear me-1"></i> Generate Payroll
                </button>
            </form>
            <?php else: ?>
            <span class="badge bg-success d-flex align-items-center"><i class="bi bi-check me-1"></i>Processed</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Employee</th><th>Department</th><th>Basic</th><th>HRA</th><th>Gross</th><th>PF</th><th>ESI</th><th>Net Pay</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($payrolls as $pr): ?>
                <tr>
                    <td>
                        <div class="emp-cell">
                            <div class="avatar-circle" style="background:<?= avatarColor($pr['employee_name']) ?>;width:30px;height:30px;font-size:10px">
                                <?= initials($pr['employee_name']) ?>
                            </div>
                            <span class="name"><?= e($pr['employee_name']) ?></span>
                        </div>
                    </td>
                    <td><?= e($pr['department_name'] ?? '—') ?></td>
                    <td><?= formatCurrency((float)$pr['basic']) ?></td>
                    <td><?= formatCurrency((float)$pr['hra']) ?></td>
                    <td class="fw-bold"><?= formatCurrency((float)$pr['gross']) ?></td>
                    <td class="text-danger"><?= formatCurrency((float)$pr['pf']) ?></td>
                    <td class="text-danger"><?= formatCurrency((float)$pr['esi']) ?></td>
                    <td class="text-success fw-bold"><?= formatCurrency((float)$pr['net_pay']) ?></td>
                    <td>
                        <a href="<?= url("payroll/payslip&id={$pr['id']}") ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-receipt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($payrolls)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-cash-coin" style="font-size:28px;opacity:0.3"></i><br>
                    No payroll data. Click "Generate Payroll" to process.
                </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
