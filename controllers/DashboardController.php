<?php
class DashboardController
{
    public function index(): void
    {
        requireLogin();

        $empModel   = new Employee();
        $leaveModel = new LeaveRequest();
        $taskModel  = new Task();
        $payModel   = new Payroll();

        $empCounts   = $empModel->countByStatus();
        $deptCounts  = $empModel->countByDepartment();
        $totalSalary = $empModel->getTotalSalary();
        $leaveCounts = $leaveModel->countByStatus();
        $taskCounts  = $taskModel->countByStatus();

        // Recent leaves
        $recentLeaves = $leaveModel->getAll([], 1);

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
