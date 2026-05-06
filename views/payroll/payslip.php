<?php
$pageTitle = 'Payslip';
$leaveModel2 = new LeaveRequest();
$pendingLeaveCount = $leaveModel2->countByStatus()['pending'];
require __DIR__ . '/../layouts/header.php';
$ps = $payslip;
$months = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
?>

<div class="card" style="max-width:700px;margin:0 auto">
    <div class="card-header">
        <h6><i class="bi bi-receipt me-2"></i>Payslip</h6>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-navy btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
            <a href="<?= url("payroll&month={$ps['month']}&year={$ps['year']}") ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
        </div>
    </div>
    <div class="card-body" id="payslipContent">
        <!-- Company Header -->
        <div class="text-center mb-3 pb-3" style="border-bottom:2px solid var(--gold)">
            <h4 class="font-display text-navy mb-1">Pixous Pvt. Ltd.</h4>
            <p class="text-muted mb-0" style="font-size:13px">Payslip for <?= $months[(int)$ps['month']] ?> <?= $ps['year'] ?></p>
        </div>

        <!-- Employee Info -->
        <div class="row mb-3 pb-3 border-bottom" style="font-size:13px">
            <div class="col-6"><span class="text-muted">Employee:</span> <strong><?= e($ps['employee_name']) ?></strong></div>
            <div class="col-6"><span class="text-muted">ID:</span> <strong><?= e($ps['emp_code']) ?></strong></div>
            <div class="col-6 mt-1"><span class="text-muted">Department:</span> <strong><?= e($ps['department_name'] ?? '—') ?></strong></div>
            <div class="col-6 mt-1"><span class="text-muted">Designation:</span> <strong><?= e($ps['designation_name'] ?? '—') ?></strong></div>
        </div>

        <!-- Earnings -->
        <div class="payslip-section mb-3">
            <h6 class="text-success">Earnings</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="payslip-row"><span class="label">Basic</span><span class="value"><?= formatCurrency((float)$ps['basic']) ?></span></div>
                    <div class="payslip-row"><span class="label">HRA</span><span class="value"><?= formatCurrency((float)$ps['hra']) ?></span></div>
                </div>
                <div class="col-md-6">
                    <div class="payslip-row"><span class="label">DA</span><span class="value"><?= formatCurrency((float)$ps['da']) ?></span></div>
                    <div class="payslip-row"><span class="label">Special Allowance</span><span class="value"><?= formatCurrency((float)$ps['special_allow']) ?></span></div>
                </div>
            </div>
        </div>

        <!-- Deductions -->
        <div class="payslip-section mb-3">
            <h6 class="text-danger">Deductions</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="payslip-row"><span class="label">PF (12%)</span><span class="value"><?= formatCurrency((float)$ps['pf']) ?></span></div>
                    <div class="payslip-row"><span class="label">ESI (0.75%)</span><span class="value"><?= formatCurrency((float)$ps['esi']) ?></span></div>
                </div>
                <div class="col-md-6">
                    <div class="payslip-row"><span class="label">Professional Tax</span><span class="value"><?= formatCurrency((float)$ps['prof_tax']) ?></span></div>
                    <div class="payslip-row"><span class="label">Other Deductions</span><span class="value"><?= formatCurrency((float)$ps['other_deduct']) ?></span></div>
                </div>
            </div>
        </div>

        <!-- Net Pay -->
        <div class="payslip-total">
            <span class="label">Net Pay</span>
            <span class="value"><?= formatCurrency((float)$ps['net_pay']) ?></span>
        </div>

        <!-- Bank Details -->
        <?php if (!empty($ps['bank_name'])): ?>
        <div class="mt-3 pt-3 border-top" style="font-size:12px;color:var(--gray-400)">
            <strong>Bank:</strong> <?= e($ps['bank_name']) ?> | 
            <strong>A/C:</strong> <?= e($ps['bank_account']) ?> | 
            <strong>IFSC:</strong> <?= e($ps['bank_ifsc']) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
