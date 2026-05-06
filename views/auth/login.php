<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="login-page">
    <div class="login-bg"></div>
    <div class="login-grid"></div>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="logo-mark d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:linear-gradient(135deg,var(--gold),var(--gold-light));border-radius:14px;font-family:'Playfair Display',serif;font-weight:700;font-size:26px;color:var(--navy)">
                P
            </div>
            <h3 class="font-display text-white mb-1" style="font-size:24px">Pixous HR Portal</h3>
            <p style="font-size:11px;color:var(--gray-400);letter-spacing:2px;text-transform:uppercase">Employee Management System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3" style="font-size:13px;border-radius:8px;background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2);color:#f87171">
                <i class="bi bi-exclamation-circle me-1"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('auth/login') ?>" autocomplete="off">
            <?= csrfField() ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="position-relative">
                    <input type="text" name="username" class="form-control" placeholder="Enter username"
                           value="<?= e($u ?? '') ?>" required autofocus
                           style="padding-left:40px">
                    <i class="bi bi-person position-absolute" style="left:14px;top:50%;transform:translateY(-50%);color:var(--gray-400)"></i>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" name="password" class="form-control" placeholder="Enter password"
                           required style="padding-left:40px" id="loginPass">
                    <i class="bi bi-lock position-absolute" style="left:14px;top:50%;transform:translateY(-50%);color:var(--gray-400)"></i>
                    <button type="button" class="btn btn-link position-absolute"
                            style="right:4px;top:50%;transform:translateY(-50%);color:var(--gray-400);text-decoration:none"
                            onclick="let p=document.getElementById('loginPass');p.type=p.type==='password'?'text':'password'">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="login-btn">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        <p class="text-center mt-3" style="font-size:12px;color:var(--gray-400)">
            Demo: <strong style="color:var(--gray-300)">admin</strong> / <strong style="color:var(--gray-300)">admin123</strong>
        </p>
    </div>
</div>
</body>
</html>
