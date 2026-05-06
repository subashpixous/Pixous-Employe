<?php
$pageTitle = 'Employee Details';
$leaveModel2 = new LeaveRequest();
$pendingLeaveCount = $leaveModel2->countByStatus()['pending'];
require __DIR__ . '/../layouts/header.php';
$emp = $employee;
?>

<div class="card">
    <div class="card-header">
        <h6><i class="bi bi-person-badge me-2"></i>Employee Profile</h6>
        <div class="d-flex gap-2">
            <a href="<?= url("employees/edit&id={$emp['id']}") ?>" class="btn btn-gold btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
            <a href="<?= url('employees') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <!-- Header -->
        <div class="text-center mb-4 pb-3 border-bottom">
            <div class="avatar-circle mx-auto mb-2" style="width:72px;height:72px;font-size:26px;background:<?= avatarColor($emp['name']) ?>">
                <?= initials($emp['name']) ?>
            </div>
            <h4 class="text-navy fw-700 mb-1"><?= e($emp['name']) ?></h4>
            <p class="text-muted mb-1" style="font-size:14px"><?= e($emp['designation_name'] ?? '—') ?> — <?= e($emp['department_name'] ?? '—') ?></p>
            <span class="badge-status badge-<?= e($emp['status']) ?>"><?= ucfirst(e($emp['status'])) ?></span>
        </div>

        <!-- Details Grid -->
        <div class="row">
            <?php
            $sections = [
                'Personal' => [
                    'Employee ID'    => $emp['emp_code'],
                    'Father/Husband' => $emp['father_name'] ?: '—',
                    'Gender'         => $emp['gender'],
                    'Marital Status' => $emp['marital_status'],
                    'Date of Birth'  => formatDate($emp['dob']),
                    'Date of Joining'=> formatDate($emp['doj']),
                    'Mobile'         => $emp['mobile'],
                    'Email'          => $emp['email'] ?: '—',
                    'Address'        => $emp['address'] ?: '—',
                ],
                'Employment' => [
                    'Qualification' => $emp['qualification'] ?: '—',
                    'Experience'    => $emp['experience'] ?: '—',
                    'Salary'        => formatCurrency((float)$emp['salary']),
                ],
                'Statutory' => [
                    'PAN'       => $emp['pan'] ?: '—',
                    'Aadhar'    => $emp['aadhar'] ?: '—',
                    'UAN'       => $emp['uan'] ?: '—',
                    'ESI No'    => $emp['esi_no'] ?: '—',
                ],
                'Bank' => [
                    'Bank Name'  => $emp['bank_name'] ?: '—',
                    'Account No' => $emp['bank_account'] ?: '—',
                    'IFSC'       => $emp['bank_ifsc'] ?: '—',
                ],
                'Nominee' => [
                    'Nominee Name'    => $emp['nominee_name'] ?: '—',
                    'Relationship'    => $emp['nominee_relation'] ?: '—',
                    'Nominee Address' => $emp['nominee_address'] ?: '—',
                ],
            ];
            foreach ($sections as $title => $fields):
            ?>
            <div class="col-12 mb-3">
                <h6 class="text-navy fw-700 mb-3 pb-2 border-bottom" style="font-size:12px;text-transform:uppercase;letter-spacing:1px">
                    <?= $title ?>
                </h6>
                <div class="row">
                    <?php foreach ($fields as $label => $value): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="detail-item">
                            <div class="dt"><?= e($label) ?></div>
                            <div class="dd"><?= e($value) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
