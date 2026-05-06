<?php
class Employee extends BaseModel
{
    public function getAll(array $filters = [], int $page = 1): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = "e.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['department_id'])) {
            $where[]  = "e.department_id = ?";
            $params[] = (int) $filters['department_id'];
        }
        if (!empty($filters['search'])) {
            $where[]  = "(e.name LIKE ? OR e.emp_code LIKE ? OR e.mobile LIKE ? OR e.email LIKE ?)";
            $s        = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$s, $s, $s, $s]);
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT e.*, d.name AS department_name, ds.title AS designation_name
                  FROM employees e
                  LEFT JOIN departments d  ON e.department_id  = d.id
                  LEFT JOIN designations ds ON e.designation_id = ds.id
                  {$whereSQL}
                  ORDER BY e.created_at DESC";

        return $this->paginate($query, $params, $page);
    }

    public function getAllActive(): array
    {
        $stmt = $this->db->prepare("SELECT id, emp_code, name, department_id FROM employees WHERE status = 'active' ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT e.*, d.name AS department_name, ds.title AS designation_name
             FROM employees e
             LEFT JOIN departments d  ON e.department_id  = d.id
             LEFT JOIN designations ds ON e.designation_id = ds.id
             WHERE e.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $code = $this->generateEmpCode();
        $stmt = $this->db->prepare(
            "INSERT INTO employees (emp_code, name, father_name, gender, marital_status, dob, doj,
             department_id, designation_id, qualification, experience, mobile, email, address,
             pan, aadhar, uan, esi_no, bank_name, bank_account, bank_ifsc, salary, photo,
             status, nominee_name, nominee_relation, nominee_address)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $code,
            $data['name'], $data['father_name'], $data['gender'], $data['marital_status'],
            $data['dob'] ?: null, $data['doj'] ?: null,
            $data['department_id'] ?: null, $data['designation_id'] ?: null,
            $data['qualification'], $data['experience'],
            $data['mobile'], $data['email'], $data['address'],
            strtoupper($data['pan']), $data['aadhar'], $data['uan'], $data['esi_no'],
            $data['bank_name'], $data['bank_account'], strtoupper($data['bank_ifsc']),
            $data['salary'], $data['photo'] ?? null,
            $data['status'] ?? 'active',
            $data['nominee_name'], $data['nominee_relation'], $data['nominee_address'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE employees SET
             name=?, father_name=?, gender=?, marital_status=?, dob=?, doj=?,
             department_id=?, designation_id=?, qualification=?, experience=?,
             mobile=?, email=?, address=?, pan=?, aadhar=?, uan=?, esi_no=?,
             bank_name=?, bank_account=?, bank_ifsc=?, salary=?,
             nominee_name=?, nominee_relation=?, nominee_address=?
             WHERE id=?"
        );
        return $stmt->execute([
            $data['name'], $data['father_name'], $data['gender'], $data['marital_status'],
            $data['dob'] ?: null, $data['doj'] ?: null,
            $data['department_id'] ?: null, $data['designation_id'] ?: null,
            $data['qualification'], $data['experience'],
            $data['mobile'], $data['email'], $data['address'],
            strtoupper($data['pan']), $data['aadhar'], $data['uan'], $data['esi_no'],
            $data['bank_name'], $data['bank_account'], strtoupper($data['bank_ifsc']),
            $data['salary'],
            $data['nominee_name'], $data['nominee_relation'], $data['nominee_address'],
            $id,
        ]);
    }

    public function updatePhoto(int $id, string $photo): bool
    {
        $stmt = $this->db->prepare("UPDATE employees SET photo = ? WHERE id = ?");
        return $stmt->execute([$photo, $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE employees SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM employees WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByStatus(): array
    {
        $stmt = $this->db->query("SELECT status, COUNT(*) as cnt FROM employees GROUP BY status");
        $result = ['active' => 0, 'inactive' => 0, 'total' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['status']] = (int) $row['cnt'];
            $result['total'] += (int) $row['cnt'];
        }
        return $result;
    }

    public function countByDepartment(): array
    {
        $stmt = $this->db->query(
            "SELECT d.name, COUNT(e.id) as cnt
             FROM employees e
             JOIN departments d ON e.department_id = d.id
             WHERE e.status = 'active'
             GROUP BY d.name ORDER BY cnt DESC"
        );
        return $stmt->fetchAll();
    }

    public function getTotalSalary(): float
    {
        $stmt = $this->db->query("SELECT COALESCE(SUM(salary),0) FROM employees WHERE status = 'active'");
        return (float) $stmt->fetchColumn();
    }

    private function generateEmpCode(): string
    {
        $stmt = $this->db->query("SELECT MAX(id) FROM employees");
        $maxId = (int) $stmt->fetchColumn();
        return 'EMP' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
    }

    // ── Lookup data ──
    public function getDepartments(): array
    {
        return $this->db->query("SELECT * FROM departments ORDER BY name")->fetchAll();
    }

    public function getDesignations(): array
    {
        return $this->db->query("SELECT * FROM designations ORDER BY title")->fetchAll();
    }
}
