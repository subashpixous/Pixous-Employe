    </div><!-- .page-content -->
</div><!-- .main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ── Sidebar Toggle ──
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}

// ── Global Search ──
function doGlobalSearch() {
    const q = document.getElementById('globalSearch').value.trim();
    const page = '<?= e($_GET['page'] ?? 'employees') ?>';
    if (q) window.location.href = '<?= url('') ?>' + page + '&search=' + encodeURIComponent(q);
}

// ── SweetAlert Flash ──
<?php $flash = getFlash(); if ($flash): ?>
Swal.fire({
    icon: '<?= e($flash['type']) ?>',
    title: '<?= e($flash['title']) ?>',
    text: '<?= e($flash['message']) ?>',
    confirmButtonColor: '#d4a532',
    confirmButtonText: 'OK',
    timer: 3500,
    timerProgressBar: true,
    customClass: { confirmButton: 'btn-gold' }
});
<?php endif; ?>

// ── Confirm Delete ──
function confirmDelete(formId, name) {
    Swal.fire({
        title: 'Delete ' + name + '?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#8892a0',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
    return false;
}

// ── Confirm Status Toggle ──
function confirmToggle(formId, action, name) {
    Swal.fire({
        title: action + ' ' + name + '?',
        text: 'Are you sure you want to ' + action.toLowerCase() + ' this employee?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d4a532',
        cancelButtonColor: '#8892a0',
        confirmButtonText: 'Yes, ' + action,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
    return false;
}

// ── Confirm Approve/Reject ──
function confirmLeaveAction(formId, action) {
    const label = action === 'approved' ? 'Approve' : 'Reject';
    const icon = action === 'approved' ? 'question' : 'warning';
    Swal.fire({
        title: label + ' this leave?',
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: action === 'approved' ? '#22c55e' : '#ef4444',
        confirmButtonText: 'Yes, ' + label,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
    return false;
}
</script>

</body>
</html>
