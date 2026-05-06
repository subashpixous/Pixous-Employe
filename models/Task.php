<?php
class Task extends BaseModel
{
    public function getAll(array $filters = [], int $page = 1): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = "t.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[]  = "t.priority = ?";
            $params[] = $filters['priority'];
        }
        if (!empty($filters['search'])) {
            $s        = '%' . $filters['search'] . '%';
            $where[]  = "(t.title LIKE ? OR e.name LIKE ?)";
            $params[] = $s;
            $params[] = $s;
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT t.*, e.name AS assignee_name, e.emp_code, d.name AS department_name
                  FROM tasks t
                  JOIN employees e     ON t.assigned_to    = e.id
                  LEFT JOIN departments d ON e.department_id = d.id
                  {$whereSQL}
                  ORDER BY FIELD(t.priority,'High','Medium','Low'), t.deadline ASC";

        return $this->paginate($query, $params, $page, 20);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, e.name AS assignee_name, e.emp_code
             FROM tasks t JOIN employees e ON t.assigned_to = e.id
             WHERE t.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO tasks (title, description, assigned_to, priority, status, deadline, progress, created_by)
             VALUES (?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['title'], $data['description'], $data['assigned_to'],
            $data['priority'], $data['status'] ?? 'Pending',
            $data['deadline'], $data['progress'] ?? 0,
            $data['created_by'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $progress = (int) $data['progress'];
        if ($data['status'] === 'Completed') $progress = 100;

        $stmt = $this->db->prepare(
            "UPDATE tasks SET title=?, description=?, assigned_to=?, priority=?, status=?, deadline=?, progress=? WHERE id=?"
        );
        return $stmt->execute([
            $data['title'], $data['description'], $data['assigned_to'],
            $data['priority'], $data['status'], $data['deadline'], $progress, $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByStatus(): array
    {
        $stmt   = $this->db->query("SELECT status, COUNT(*) as cnt FROM tasks GROUP BY status");
        $result = ['Pending' => 0, 'In Progress' => 0, 'Completed' => 0, 'total' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['status']] = (int) $row['cnt'];
            $result['total'] += (int) $row['cnt'];
        }
        return $result;
    }
}
