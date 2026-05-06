<?php
// $data = form values, $errors = validation errors
// $departments, $designations = lookup arrays
// $actionUrl = form action URL, $submitLabel = button text
$d = $data;
$e = $errors ?? [];
?>

<div class="card">
    <div class="card-header">
        <h6><i class="bi bi-person-plus me-2"></i><?= e($formTitle ?? 'Employee Form') ?></h6>
        <a href="<?= url('employees') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card-body">
        <?php if (!empty($e)): ?>
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:13px">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <?php foreach ($e as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= $actionUrl ?>" enctype="multipart/form-data" novalidate>
            <?= csrfField() ?>

            <!-- Personal Info -->
            <h6 class="text-navy fw-700 mb-3 pb-2 border-bottom" style="font-size:13px;text-transform:uppercase;letter-spacing:1px">
                <i class="bi bi-person me-1"></i> Personal Information
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input name="name" class="form-control <?= isset($e['name'])?'is-invalid':'' ?>" value="<?= e($d['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Father/Husband Name</label>
                    <input name="father_name" class="form-control" value="<?= e($d['father_name'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <?php foreach (['Male','Female','Other'] as $g): ?>
                        <option <?= ($d['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Marital Status</label>
                    <select name="marital_status" class="form-select">
                        <option <?= ($d['marital_status'] ?? '') === 'Unmarried' ? 'selected' : '' ?>>Unmarried</option>
                        <option <?= ($d['marital_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?= e($d['dob'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date of Joining</label>
                    <input type="date" name="doj" class="form-control" value="<?= e($d['doj'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile *</label>
                    <input name="mobile" class="form-control <?= isset($e['mobile'])?'is-invalid':'' ?>" value="<?= e($d['mobile'] ?? '') ?>" maxlength="10" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control <?= isset($e['email'])?'is-invalid':'' ?>" value="<?= e($d['email'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= e($d['address'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Employment -->
            <h6 class="text-navy fw-700 mb-3 pb-2 border-bottom" style="font-size:13px;text-transform:uppercase;letter-spacing:1px">
                <i class="bi bi-briefcase me-1"></i> Employment Details
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">Select</option>
                        <?php foreach ($departments as $dep): ?>
                        <option value="<?= $dep['id'] ?>" <?= ($d['department_id'] ?? 0) == $dep['id'] ? 'selected' : '' ?>><?= e($dep['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Designation</label>
                    <select name="designation_id" class="form-select">
                        <option value="">Select</option>
                        <?php foreach ($designations as $des): ?>
                        <option value="<?= $des['id'] ?>" <?= ($d['designation_id'] ?? 0) == $des['id'] ? 'selected' : '' ?>><?= e($des['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salary (₹)</label>
                    <input type="number" name="salary" class="form-control" value="<?= e($d['salary'] ?? 0) ?>" min="0" step="500">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Qualification</label>
                    <input name="qualification" class="form-control" value="<?= e($d['qualification'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Experience</label>
                    <input name="experience" class="form-control" value="<?= e($d['experience'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control <?= isset($e['photo'])?'is-invalid':'' ?>" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small class="text-muted">Max 2MB · JPG, PNG, GIF, WebP</small>
                </div>
            </div>

            <!-- Statutory -->
            <h6 class="text-navy fw-700 mb-3 pb-2 border-bottom" style="font-size:13px;text-transform:uppercase;letter-spacing:1px">
                <i class="bi bi-shield-check me-1"></i> Statutory & Bank Details
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">PAN</label>
                    <input name="pan" class="form-control text-uppercase <?= isset($e['pan'])?'is-invalid':'' ?>" value="<?= e($d['pan'] ?? '') ?>" maxlength="10">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Aadhar No</label>
                    <input name="aadhar" class="form-control <?= isset($e['aadhar'])?'is-invalid':'' ?>" value="<?= e($d['aadhar'] ?? '') ?>" maxlength="12">
                </div>
                <div class="col-md-4">
                    <label class="form-label">UAN</label>
                    <input name="uan" class="form-control" value="<?= e($d['uan'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ESI No</label>
                    <input name="esi_no" class="form-control" value="<?= e($d['esi_no'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bank Name</label>
                    <input name="bank_name" class="form-control" value="<?= e($d['bank_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account No</label>
                    <input name="bank_account" class="form-control" value="<?= e($d['bank_account'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">IFSC Code</label>
                    <input name="bank_ifsc" class="form-control text-uppercase" value="<?= e($d['bank_ifsc'] ?? '') ?>">
                </div>
            </div>

            <!-- Nominee -->
            <h6 class="text-navy fw-700 mb-3 pb-2 border-bottom" style="font-size:13px;text-transform:uppercase;letter-spacing:1px">
                <i class="bi bi-heart me-1"></i> Nominee Details
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Nominee Name</label>
                    <input name="nominee_name" class="form-control" value="<?= e($d['nominee_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Relationship</label>
                    <input name="nominee_relation" class="form-control" value="<?= e($d['nominee_relation'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nominee Address</label>
                    <input name="nominee_address" class="form-control" value="<?= e($d['nominee_address'] ?? '') ?>">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= url('employees') ?>" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-check-lg me-1"></i> <?= e($submitLabel ?? 'Save') ?>
                </button>
            </div>
        </form>
    </div>
</div>
