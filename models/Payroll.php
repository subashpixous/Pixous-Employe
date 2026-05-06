<?php
class Payroll extends BaseModel
{
    public function getAll(int $month, int $year, string $search = '', int $page = 1): array
    {
        $where  = ["p.month = ?", "p.year = ?"];
        $params = [$month, $year];

        if ($search) {
            $s        = '%' . $search . '%';
            $where[]  = "(e.name LIKE ? OR e.emp_code LIKE ?)";
            $params[] = $s;
            $params[] = $s;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $query = "SELECT p.*, e.name AS employee_name, e.emp_code, d.name AS department_name,
                         ds.title AS designation_name
                  FROM payroll p
                  JOIN employees e     ON p.employee_id   = e.id
                  LEFT JOIN departments d  ON e.department_id  = d.id
                  LEFT JOIN designations ds ON e.designation_id = ds.id
                  {$whereSQL}
                  ORDER BY e.name ASC";

        return $this->paginate($query, $params, $page, 50);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, e.name AS employee_name, e.emp_code, e.bank_name, e.bank_account, e.bank_ifsc,
                    d.name AS department_name, ds.title AS designation_name
             FROM payroll p
             JOIN employees e     ON p.employee_id   = e.id
             LEFT JOIN departments d  ON e.department_id  = d.id
             LEFT JOIN designations ds ON e.designation_id = ds.id
             WHERE p.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function generateForMonth(int $month, int $year, int $processedBy): int
    {
        // Get all active employees without payroll for this period
        $stmt = $this->db->prepare(
            "SELECT e.id, e.salary FROM employees e
             WHERE e.status = 'active'
             AND e.id NOT IN (SELECT employee_id FROM payroll WHERE month = ? AND year = ?)"
        );
        $stmt->execute([$month, $year]);
        $employees = $stmt->fetchAll();

        $insert = $this->db->prepare(
            "INSERT INTO payroll (employee_id, month, year, basic, hra, da, special_allow, gross, pf, esi, prof_tax, net_pay, status, processed_by)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );

        $count = 0;
        foreach ($employees as $emp) {
            $sal     = (float) $emp['salary'];
            $basic   = round($sal * 0.50, 2);
            $hra     = round($sal * 0.20, 2);
            $da      = round($sal * 0.15, 2);
            $special = round($sal - $basic - $hra - $da, 2);
            $pf      = round($basic * 0.12, 2);
            $esi     = $sal <= 21000 ? round($sal * 0.0075, 2) : 0;
            $pt      = $sal > 15000 ? 200 : 0;
            $net     = round($sal - $pf - $esi - $pt, 2);

            $insert->execute([
                $emp['id'], $month, $year,
                $basic, $hra, $da, $special, $sal,
                $pf, $esi, $pt, $net,
                'processed', $processedBy,
            ]);
            $count++;
        }

        return $count;
    }

    public function getSummary(int $month, int $year): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count,
                    COALESCE(SUM(gross),0) as total_gross,
                    COALESCE(SUM(pf),0) as total_pf,
                    COALESCE(SUM(esi),0) as total_esi,
                    COALESCE(SUM(prof_tax),0) as total_pt,
                    COALESCE(SUM(net_pay),0) as total_net
             FROM payroll WHERE month = ? AND year = ?"
        );
        $stmt->execute([$month, $year]);
        return $stmt->fetch();
    }

    public function exists(int $month, int $year): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payroll WHERE month = ? AND year = ?");
        $stmt->execute([$month, $year]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
