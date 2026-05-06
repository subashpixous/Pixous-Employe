<?php
class TaskController
{
    private Task $model;

    public function __construct()
    {
        $this->model = new Task();
    }

    public function index(): void
    {
        requireLogin();

        $filters = [
            'status'   => sanitize($_GET['status'] ?? ''),
            'priority' => sanitize($_GET['priority'] ?? ''),
            'search'   => sanitize($_GET['search'] ?? ''),
        ];
        $page = max(1, sanitizeInt($_GET['pg'] ?? 1));

        $result     = $this->model->getAll($filters, $page);
        $tasks      = $result['data'];
        $pagination = $result;
        $counts     = $this->model->countByStatus();
        $empModel   = new Employee();
        $activeEmps = $empModel->getAllActive();

        require __DIR__ . '/../views/tasks/index.php';
    }

    public function store(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $data = [
                'title'       => sanitize($_POST['title'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'assigned_to' => sanitizeInt($_POST['assigned_to'] ?? 0),
                'priority'    => sanitize($_POST['priority'] ?? 'Medium'),
                'status'      => sanitize($_POST['status'] ?? 'Pending'),
                'deadline'    => sanitize($_POST['deadline'] ?? ''),
                'progress'    => sanitizeInt($_POST['progress'] ?? 0),
                'created_by'  => $_SESSION['user_id'],
            ];

            $errors = validateRequired(['title'=>'Title','assigned_to'=>'Assignee','deadline'=>'Deadline'], array_map('strval', $data));

            if (empty($errors)) {
                $this->model->create($data);
                logActivity(Database::getInstance()->getConnection(), 'Create', 'Task', "Created task: {$data['title']}");
                setFlash('success', 'Task Created!', 'New task has been assigned.');
            } else {
                setFlash('error', 'Validation Error', implode(' ', $errors));
            }
        }
        redirect('tasks');
    }

    public function update(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $id   = sanitizeInt($_POST['id'] ?? 0);
            $data = [
                'title'       => sanitize($_POST['title'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'assigned_to' => sanitizeInt($_POST['assigned_to'] ?? 0),
                'priority'    => sanitize($_POST['priority'] ?? 'Medium'),
                'status'      => sanitize($_POST['status'] ?? 'Pending'),
                'deadline'    => sanitize($_POST['deadline'] ?? ''),
                'progress'    => sanitizeInt($_POST['progress'] ?? 0),
            ];

            if ($id > 0) {
                $this->model->update($id, $data);
                logActivity(Database::getInstance()->getConnection(), 'Update', 'Task', "Updated task #{$id}");
                setFlash('success', 'Updated!', 'Task has been updated.');
            }
        }
        redirect('tasks');
    }

    public function delete(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $id = sanitizeInt($_POST['id'] ?? 0);
            if ($id > 0) {
                $this->model->delete($id);
                logActivity(Database::getInstance()->getConnection(), 'Delete', 'Task', "Deleted task #{$id}");
                setFlash('success', 'Deleted!', 'Task has been removed.');
            }
        }
        redirect('tasks');
    }
}
