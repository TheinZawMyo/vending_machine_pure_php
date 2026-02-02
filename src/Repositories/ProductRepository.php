<?php

namespace Theinzawmyo\VendingMachine\Repositories;

use PDO;
use Theinzawmyo\VendingMachine\Database\Connection;
use Theinzawmyo\VendingMachine\Models\Product;

class ProductRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::get();
    }

    public function find(int $id): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Product($row) : null;
    }

    /**
     * @return Product[]
     */
    public function findAll(int $limit = 100, int $offset = 0, string $orderBy = 'id', string $orderDir = 'ASC'): array
    {
        $allowed = ['id', 'name', 'price', 'quantity', 'created_at'];
        $orderBy = in_array($orderBy, $allowed) ? $orderBy : 'id';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM products ORDER BY {$orderBy} {$orderDir} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Product($row);
        }
        return $result;
    }

    public function count(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM products');
        return (int) $stmt->fetchColumn();
    }

    /** Count products with quantity below threshold (default 10). */
    public function countLowStock(int $threshold = 10): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM products WHERE quantity < ?');
        $stmt->execute([$threshold]);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): Product
    {
        $sql = 'INSERT INTO products (name, price, quantity) VALUES (:name, :price, :quantity)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':quantity' => (int) $data['quantity'],
        ]);
        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id);
    }

    public function update(int $id, array $data): ?Product
    {
        $sql = 'UPDATE products SET name = :name, price = :price, quantity = :quantity WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':quantity' => (int) $data['quantity'],
        ]);
        return $stmt->rowCount() ? $this->find($id) : null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function decrementQuantity(int $id, int $by): bool
    {
        $stmt = $this->pdo->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?');
        $stmt->execute([$by, $id, $by]);
        return $stmt->rowCount() > 0;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
