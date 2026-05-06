<?php
class EmployeeController
{
    private Employee $model;

    public function __construct()
    {
        $this->model = new Employee();
    }

    public function index(): void
    {
        requireLogin();

        $filters = [
            'status'        => sanitize($_GET['status'] ?? ''),
            'department_id' => sanitizeInt($_GET['department_id'] ?? 0),
            'search'        => sanitize($_GET['search'] ?? ''),
        ];
        $page = max(1, sanitizeInt($_GET['pg'] ?? 1));

        $result       = $this->model->getAll($filters, $page);
        $employees    = $result['data'];
        $pagination   = $result;
        $departments  = $this->model->getDepartments();
        $designations = $this->model->getDesignations();
        $counts       = $this->model->countByStatus();

        require __DIR__ . '/../views/employees/index.php';
    }

    public function create(): void
    {
        requireLogin();
        $departments  = $this->model->getDepartments();
        $designations = $this->model->getDesignations();
        $errors       = [];
        $data         = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRF()) {
                $errors['csrf'] = 'Security token invalid.';
            } else {
                $data = $this->sanitizeFormData($_POST);
                $errors = $this->validateFormData($data);

                // Photo upload
                if (!empty($_FILES['photo']['name'])) {
                    $upload = uploadFile($_FILES['photo'], 'photos', ALLOWED_IMAGE_TYPES);
                    if ($upload['success']) {
                        $data['photo'] = $upload['path'];
                    } else {
                        $errors['photo'] = $upload['error'];
                    }
                }

                if (empty($errors)) {
                    $id = $this->model->create($data);
                    logActivity(Database::getInstance()->getConnection(), 'Create', 'Employee', "Created employee: {$data['name']}");
                    setFlash('success', 'Employee Created!', "{$data['name']} has been added successfully.");
                    redirect('employees');
                }
            }
        }

        require __DIR__ . '/../views/employees/create.php';
    }

    public function edit(): void
    {
        requireLogin();
        $id       = sanitizeInt($_GET['id'] ?? 0);
        $employee = $this->model->find($id);

        if (!$employee) {
            setFlash('error', 'Not Found', 'Employee not found.');
            redirect('employees');
        }

        $departments  = $this->model->getDepartments();
        $designations = $this->model->getDesignations();
        $errors       = [];
        $data         = $employee;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRF()) {
                $errors['csrf'] = 'Security token invalid.';
            } else {
                $data   = $this->sanitizeFormData($_POST);
                $errors = $this->validateFormData($data);

                // Photo upload
                if (!empty($_FILES['photo']['name'])) {
                    $upload = uploadFile($_FILES['photo'], 'photos', ALLOWED_IMAGE_TYPES);
                    if ($upload['success']) {
                        $this->model->updatePhoto($id, $upload['path']);
                    } else {
                        $errors['photo'] = $upload['error'];
                    }
                }

                if (empty($errors)) {
                    $this->model->update($id, $data);
                    logActivity(Database::getInstance()->getConnection(), 'Update', 'Employee', "Updated employee #{$id}: {$data['name']}");
                    setFlash('success', 'Updated!', "{$data['name']}'s record has been updated.");
                    redirect('employees');
                }
            }
        }

        require __DIR__ . '/../views/employees/edit.php';
    }

    public function view(): void
    {
        requireLogin();
        $id       = sanitizeInt($_GET['id'] ?? 0);
        $employee = $this->model->find($id);

        if (!$employee) {
            setFlash('error', 'Not Found', 'Employee not found.');
            redirect('employees');
        }

        require __DIR__ . '/../views/employees/view.php';
    }

    public function toggleStatus(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $id     = sanitizeInt($_POST['id'] ?? 0);
            $status = sanitize($_POST['status'] ?? '');

            if (in_array($status, ['active', 'inactive']) && $id > 0) {
                $this->model->updateStatus($id, $status);
                $action = $status === 'active' ? 'Reactivated' : 'Deactivated';
                logActivity(Database::getInstance()->getConnection(), $action, 'Employee', "Employee #{$id} {$action}");
                setFlash('success', "{$action}!", "Employee has been {$action} successfully.");
            }
        }
        redirect('employees');
    }

    public function delete(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF()) {
            $id = sanitizeInt($_POST['id'] ?? 0);
            if ($id > 0) {
                $emp = $this->model->find($id);
                $this->model->delete($id);
                logActivity(Database::getInstance()->getConnection(), 'Delete', 'Employee', "Deleted employee #{$id}: " . ($emp['name'] ?? ''));
                setFlash('success', 'Deleted!', 'Employee has been removed from the system.');
            }
        }
        redirect('employees');
    }

    // ── Private Helpers ──
    private function sanitizeFormData(array $post): array
    {
        return [
            'name'            => sanitize($post['name'] ?? ''),
            'father_name'     => sanitize($post['father_name'] ?? ''),
            'gender'          => sanitize($post['gender'] ?? 'Male'),
            'marital_status'  => sanitize($post['marital_status'] ?? 'Unmarried'),
            'dob'             => sanitize($post['dob'] ?? ''),
            'doj'             => sanitize($post['doj'] ?? ''),
            'department_id'   => sanitizeInt($post['department_id'] ?? 0),
            'designation_id'  => sanitizeInt($post['designation_id'] ?? 0),
            'qualification'   => sanitize($post['qualification'] ?? ''),
            'experience'      => sanitize($post['experience'] ?? ''),
            'mobile'          => sanitize($post['mobile'] ?? ''),
            'email'           => sanitizeEmail($post['email'] ?? ''),
            'address'         => sanitize($post['address'] ?? ''),
            'pan'             => sanitize($post['pan'] ?? ''),
            'aadhar'          => sanitize($post['aadhar'] ?? ''),
            'uan'             => sanitize($post['uan'] ?? ''),
            'esi_no'          => sanitize($post['esi_no'] ?? ''),
            'bank_name'       => sanitize($post['bank_name'] ?? ''),
            'bank_account'    => sanitize($post['bank_account'] ?? ''),
            'bank_ifsc'       => sanitize($post['bank_ifsc'] ?? ''),
            'salary'          => sanitizeFloat($post['salary'] ?? 0),
            'status'          => sanitize($post['status'] ?? 'active'),
            'nominee_name'    => sanitize($post['nominee_name'] ?? ''),
            'nominee_relation'=> sanitize($post['nominee_relation'] ?? ''),
            'nominee_address' => sanitize($post['nominee_address'] ?? ''),
            'photo'           => null,
        ];
    }

    private function validateFormData(array $data): array
    {
        $errors = validateRequired([
            'name'   => 'Full Name',
            'mobile' => 'Mobile Number',
        ], $data);

        if (!empty($data['mobile']) && !validateMobile($data['mobile'])) {
            $errors['mobile'] = 'Enter a valid 10-digit Indian mobile number.';
        }
        if (!empty($data['pan']) && !validatePAN($data['pan'])) {
            $errors['pan'] = 'Enter a valid PAN (e.g., ABCDE1234F).';
        }
        if (!empty($data['aadhar']) && !validateAadhar($data['aadhar'])) {
            $errors['aadhar'] = 'Enter a valid 12-digit Aadhar number.';
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if ($data['salary'] < 0) {
            $errors['salary'] = 'Salary cannot be negative.';
        }

        return $errors;
    }
}
