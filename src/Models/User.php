<?php

namespace Theinzawmyo\VendingMachine\Models;

/**
 * User entity: id, username, role (admin|user).
 */
class User
{
    public int $id;
    public string $username;
    public string $role;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    public function __construct(array $row = [])
    {
        if (!empty($row)) {
            $this->id = (int) ($row['id'] ?? 0);
            $this->username = (string) ($row['username'] ?? '');
            $this->role = (string) ($row['role'] ?? 'user');
            $this->createdAt = $row['created_at'] ?? null;
            $this->updatedAt = $row['updated_at'] ?? null;
        }
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'role' => $this->role,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
