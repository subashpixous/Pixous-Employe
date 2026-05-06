<?php
class LeaveController
{
    private LeaveRequest $model;

    public function __construct()
    {
        $this->model = new LeaveRequest();
    }

    public function index(): void
    {
        requireLogin();

        $filters = [
            'status' => sanitize($_GET['status'] ?? ''),
            'search' => sanitize($_GET['search'] ?? ''),
        ];
        $page = max(1, sanitizeInt($_GET['pg'] ?? 1));

        $result      = $this->model->getAll($filters, $page);
        $leaves      = $result['data'];
        $pagination  = $result;
        $counts      = $this->model->countByStatus();
        $leaveTypes  = $this->model->getLeaveTypes();
        $empModel    = new Employee();
        $activeEmps  = $empModel->getAllActive();

        require __DIR__ . '/../views/leaves/index.php';
    }

    public function store(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $data = [
                'employee_id'   => sanitizeInt($_POST['employee_id'] ?? 0),
                'leave_type_id' => sanitizeInt($_POST['leave_type_id'] ?? 0),
                'date_from'     => sanitize($_POST['date_from'] ?? ''),
                'date_to'       => sanitize($_POST['date_to'] ?? ''),
                'reason'        => sanitize($_POST['reason'] ?? ''),
            ];

            $errors = validateRequired([
                'employee_id'   => 'Employee',
                'leave_type_id' => 'Leave Type',
                'date_from'     => 'From Date',
                'date_to'       => 'To Date',
                'reason'        => 'Reason',
            ], array_map('strval', $data));

            if (empty($errors)) {
                $this->model->create($data);
                logActivity(Database::getInstance()->getConnection(), 'Create', 'Leave', "Leave request created for employee #{$data['employee_id']}");
                setFlash('success', 'Submitted!', 'Leave request has been submitted.');
            } else {
                setFlash('error', 'Validation Error', implode(' ', $errors));
            }
        }
        redirect('leaves');
    }

    public function approve(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $id      = sanitizeInt($_POST['id'] ?? 0);
            $action  = sanitize($_POST['action'] ?? '');
            $remarks = sanitize($_POST['remarks'] ?? '');

            if (in_array($action, ['approved', 'rejected']) && $id > 0) {
                $this->model->updateStatus($id, $action, $_SESSION['user_id'], $remarks);
                $label = ucfirst($action);
                logActivity(Database::getInstance()->getConnection(), $label, 'Leave', "Leave #{$id} {$label}");
                setFlash('success', "{$label}!", "Leave request has been {$action}.");
            }
        }
        redirect('leaves');
    }

    public function delete(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $id = sanitizeInt($_POST['id'] ?? 0);
            if ($id > 0) {
                $this->model->delete($id);
                setFlash('success', 'Deleted!', 'Leave request removed.');
            }
        }
        redirect('leaves');
    }
}
