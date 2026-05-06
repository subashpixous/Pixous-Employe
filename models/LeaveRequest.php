<?php
class LeaveRequest extends BaseModel
{
    public function getAll(array $filters = [], int $page = 1): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = "lr.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $s        = '%' . $filters['search'] . '%';
            $where[]  = "(e.name LIKE ? OR lt.name LIKE ?)";
            $params[] = $s;
            $params[] = $s;
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT lr.*, e.name AS employee_name, e.emp_code, lt.name AS leave_type,
                         d.name AS department_name
                  FROM leave_requests lr
                  JOIN employees e    ON lr.employee_id  = e.id
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  LEFT JOIN departments d ON e.department_id = d.id
                  {$whereSQL}
                  ORDER BY lr.applied_on DESC";

        return $this->paginate($query, $params, $page);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT lr.*, e.name AS employee_name, e.emp_code, lt.name AS leave_type
             FROM leave_requests lr
             JOIN employees e    ON lr.employee_id  = e.id
             JOIN leave_types lt ON lr.leave_type_id = lt.id
             WHERE lr.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $d1   = new DateTime($data['date_from']);
        $d2   = new DateTime($data['date_to']);
        $days = max(1, $d2->diff($d1)->days + 1);

        $stmt = $this->db->prepare(
            "INSERT INTO leave_requests (employee_id, leave_type_id, date_from, date_to, days, reason, status)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['employee_id'], $data['leave_type_id'],
            $data['date_from'], $data['date_to'], $days,
            $data['reason'], 'pending',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, int $approvedBy, string $remarks = ''): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE leave_requests SET status = ?, approved_by = ?, remarks = ? WHERE id = ?"
        );
        return $stmt->execute([$status, $approvedBy, $remarks, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM leave_requests WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByStatus(): array
    {
        $stmt   = $this->db->query("SELECT status, COUNT(*) as cnt FROM leave_requests GROUP BY status");
        $result = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['status']] = (int) $row['cnt'];
            $result['total'] += (int) $row['cnt'];
        }
        return $result;
    }

    public function getLeaveTypes(): array
    {
        return $this->db->query("SELECT * FROM leave_types ORDER BY name")->fetchAll();
    }
}
