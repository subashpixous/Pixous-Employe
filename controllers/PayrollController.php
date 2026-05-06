<?php
class PayrollController
{
    private Payroll $model;

    public function __construct()
    {
        $this->model = new Payroll();
    }

    public function index(): void
    {
        requireLogin();

        $month  = sanitizeInt($_GET['month'] ?? date('n'));
        $year   = sanitizeInt($_GET['year']  ?? date('Y'));
        $search = sanitize($_GET['search'] ?? '');
        $page   = max(1, sanitizeInt($_GET['pg'] ?? 1));

        if ($month < 1 || $month > 12) $month = date('n');
        if ($year < 2020 || $year > 2030) $year = date('Y');

        $result    = $this->model->getAll($month, $year, $search, $page);
        $payrolls  = $result['data'];
        $pagination= $result;
        $summary   = $this->model->getSummary($month, $year);
        $hasData   = $this->model->exists($month, $year);

        require __DIR__ . '/../views/payroll/index.php';
    }

    public function generate(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $month = sanitizeInt($_POST['month'] ?? date('n'));
            $year  = sanitizeInt($_POST['year']  ?? date('Y'));

            $count = $this->model->generateForMonth($month, $year, $_SESSION['user_id']);
            logActivity(Database::getInstance()->getConnection(), 'Generate', 'Payroll', "Generated payroll for {$month}/{$year}: {$count} employees");

            if ($count > 0) {
                setFlash('success', 'Payroll Processed!', "Payroll generated for {$count} employees.");
            } else {
                setFlash('info', 'Already Processed', 'All employees already have payroll for this period.');
            }
            redirect("payroll&month={$month}&year={$year}");
        }
        redirect('payroll');
    }

    public function payslip(): void
    {
        requireLogin();
        $id      = sanitizeInt($_GET['id'] ?? 0);
        $payslip = $this->model->find($id);

        if (!$payslip) {
            setFlash('error', 'Not Found', 'Payslip not found.');
            redirect('payroll');
        }

        require __DIR__ . '/../views/payroll/payslip.php';
    }
}
