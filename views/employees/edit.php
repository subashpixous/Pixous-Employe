<?php
$pageTitle = 'Edit Employee';
$leaveModel2 = new LeaveRequest();
$pendingLeaveCount = $leaveModel2->countByStatus()['pending'];
require __DIR__ . '/../layouts/header.php';

$formTitle   = 'Edit Employee — ' . e($employee['name']);
$actionUrl   = url("employees/edit&id={$employee['id']}");
$submitLabel = 'Update Employee';
require __DIR__ . '/_form.php';

require __DIR__ . '/../layouts/footer.php';
?>
