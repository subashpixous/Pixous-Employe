<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> — <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-mark">P</div>
        <div>
            <h4>Pixous</h4>
            <small>Admin Portal</small>
        </div>
    </div>

    <div class="sidebar-nav">
        <?php
        $currentPage = $_GET['page'] ?? 'dashboard';
        $navItems = [
            ['page'=>'dashboard', 'icon'=>'bi-grid-1x2-fill',    'label'=>'Dashboard'],
            ['page'=>'employees', 'icon'=>'bi-people-fill',      'label'=>'Employees'],
            ['page'=>'leaves',    'icon'=>'bi-calendar2-check',  'label'=>'Leave Mgmt',   'badge' => $pendingLeaveCount ?? 0],
            ['page'=>'payroll',   'icon'=>'bi-cash-coin',        'label'=>'Payroll'],
            ['page'=>'tasks',     'icon'=>'bi-check2-square',    'label'=>'Task Monitor'],
        ];
        foreach ($navItems as $nav):
            $isActive = ($currentPage === $nav['page']) ? 'active' : '';
        ?>
        <a href="<?= url($nav['page']) ?>" class="nav-link sidebar-link <?= $isActive ?>">
            <i class="bi <?= $nav['icon'] ?>"></i>
            <span><?= $nav['label'] ?></span>
            <?php if (!empty($nav['badge']) && $nav['badge'] > 0): ?>
                <span class="badge bg-danger rounded-pill"><?= (int)$nav['badge'] ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="sidebar-footer">
        <a href="<?= url('auth/logout') ?>" class="nav-link sidebar-link">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-hamburger" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h5><?= e($pageTitle ?? 'Dashboard') ?></h5>
        </div>
        <div class="topbar-right">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="globalSearch" placeholder="Search..." onkeydown="if(event.key==='Enter')doGlobalSearch()">
            </div>
            <div class="position-relative">
                <a href="<?= url('leaves') ?>" class="btn btn-link p-1" style="color:var(--gray-400);font-size:18px">
                    <i class="bi bi-bell"></i>
                    <?php if (($pendingLeaveCount ?? 0) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:9px">
                            <?= (int)($pendingLeaveCount ?? 0) ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="avatar-circle" style="background:var(--navy)">
                <?= initials(currentUser()['full_name'] ?? 'AD') ?>
            </div>
        </div>
    </header>

    <div class="page-content">
