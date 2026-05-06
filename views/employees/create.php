<?php
$pageTitle = 'Add Employee';
$leaveModel2 = new LeaveRequest();
$pendingLeaveCount = $leaveModel2->countByStatus()['pending'];
require __DIR__ . '/../layouts/header.php';

$formTitle   = 'Add New Employee';
$actionUrl   = url('employees/create');
$submitLabel = 'Create Employee';
require __DIR__ . '/_form.php';

require __DIR__ . '/../layouts/footer.php';
?>
