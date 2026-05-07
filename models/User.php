<?php

class User extends BaseModel
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE username = ? 
            LIMIT 1
        ");

        $stmt->execute([$username]);

        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE id = ? 
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET updated_at = NOW() 
            WHERE id = ?
        ");

        $stmt->execute([$id]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}