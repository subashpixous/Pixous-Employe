<?php

class User extends BaseModel
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE username = ? AND is_active = 1 LIMIT 1"
        );

        $stmt->execute([$username]);

        return $stmt->fetch() ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return $password === $hash;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET last_login = NOW() WHERE id = ?"
        );

        $stmt->execute([$id]);
    }
}