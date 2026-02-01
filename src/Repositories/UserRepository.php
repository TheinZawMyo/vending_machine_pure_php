<?php

namespace Theinzawmyo\VendingMachine\Repositories;

use PDO;
use Theinzawmyo\VendingMachine\Database\Connection;
use Theinzawmyo\VendingMachine\Models\User;

class UserRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::get();
    }

    public function find(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT id, username, role, created_at, updated_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare('SELECT id, username, role, created_at, updated_at FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row) : null;
    }

    public function getPasswordHash(int $userId): ?string
    {
        $stmt = $this->pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['password'] : null;
    }

    public function verifyPassword(string $username, string $password): ?User
    {
        $stmt = $this->pdo->prepare('SELECT id, username, password, role, created_at, updated_at FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($password, $row['password'])) {
            return null;
        }
        unset($row['password']);
        return new User($row);
    }

    /**
     * @return User[]
     */
    public function findAll(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, role, created_at, updated_at FROM users ORDER BY id ASC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new User($row);
        }
        return $result;
    }

    public function count(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM users');
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): User
    {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
        $stmt->execute([
            ':username' => $data['username'],
            ':password' => $hash,
            ':role' => $data['role'] ?? 'user',
        ]);
        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id);
    }

    public function update(int $id, array $data): ?User
    {
        $fields = ['username' => $data['username'] ?? null, 'role' => $data['role'] ?? null];
        if (!empty($data['password'])) {
            $fields['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $set = [];
        $params = [':id' => $id];
        foreach ($fields as $k => $v) {
            if ($v !== null) {
                $set[] = "{$k} = :{$k}";
                $params[":{$k}"] = $v;
            }
        }
        if (empty($set)) {
            return $this->find($id);
        }
        $stmt = $this->pdo->prepare('UPDATE users SET ' . implode(', ', $set) . ' WHERE id = :id');
        $stmt->execute($params);
        return $stmt->rowCount() ? $this->find($id) : null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
