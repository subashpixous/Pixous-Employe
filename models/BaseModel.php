<?php
/**
 * Base Model — shared DB access for all models
 */
class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function paginate(string $query, array $params, int $page, int $perPage = PER_PAGE): array
    {
        // Count total
        $countQuery = preg_replace('/SELECT .+? FROM/i', 'SELECT COUNT(*) as total FROM', $query);
        $countQuery = preg_replace('/ORDER BY .+$/i', '', $countQuery);
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        // Fetch page
        $offset = ($page - 1) * $perPage;
        $query .= " LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return [
            'data'       => $stmt->fetchAll(),
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'total_pages'=> max(1, (int) ceil($total / $perPage)),
        ];
    }
}
