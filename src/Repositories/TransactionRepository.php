<?php

namespace Theinzawmyo\VendingMachine\Repositories;

use PDO;
use Theinzawmyo\VendingMachine\Database\Connection;
use Theinzawmyo\VendingMachine\Models\Transaction;

class TransactionRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::get();
    }

    public function find(int $id): ?Transaction
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transactions WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Transaction($row) : null;
    }

    public function create(int $productId, ?int $userId, int $quantity, string $totalAmount): Transaction
    {
        $stmt = $this->pdo->prepare('INSERT INTO transactions (product_id, user_id, quantity, total) VALUES (:product_id, :user_id, :quantity, :total)');
        $stmt->execute([
            ':product_id' => $productId,
            ':user_id' => $userId,
            ':quantity' => $quantity,
            ':total' => $totalAmount,
        ]);
        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id);
    }

    /**
     * @return Transaction[]
     */
    public function findByProduct(int $productId, int $limit = 50): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transactions WHERE product_id = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $productId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Transaction($row);
        }
        return $result;
    }

    /**
     * @return Transaction[]
     */
    public function findByUserId(int $userId, int $perPage, int $offset): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transactions WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Transaction($row);
        }
        return $result;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function countByUserId(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM transactions WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * @param int|null $productId
     * @param int|null $userId
     * @param string|null $dateFrom Y-m-d
     * @param string|null $dateTo Y-m-d
     * @return Transaction[]
     */
    public function findAll(int $limit = 50, int $offset = 0, ?int $productId = null, ?int $userId = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $sql = 'SELECT t.* FROM transactions t WHERE 1=1';
        $params = [];
        if ($productId !== null) {
            $sql .= ' AND t.product_id = :product_id';
            $params[':product_id'] = $productId;
        }
        if ($userId !== null) {
            $sql .= ' AND t.user_id = :user_id';
            $params[':user_id'] = $userId;
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            $sql .= ' AND DATE(t.created_at) >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo !== null && $dateTo !== '') {
            $sql .= ' AND DATE(t.created_at) <= :date_to';
            $params[':date_to'] = $dateTo;
        }
        $sql .= ' ORDER BY t.created_at DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Transaction($row);
        }
        return $result;
    }

    /**
     * @param int|null $productId
     * @param int|null $userId
     * @param string|null $dateFrom Y-m-d
     * @param string|null $dateTo Y-m-d
     */
    public function countAll(?int $productId = null, ?int $userId = null, ?string $dateFrom = null, ?string $dateTo = null): int
    {
        $sql = 'SELECT COUNT(*) FROM transactions t WHERE 1=1';
        $params = [];
        if ($productId !== null) {
            $sql .= ' AND t.product_id = :product_id';
            $params[':product_id'] = $productId;
        }
        if ($userId !== null) {
            $sql .= ' AND t.user_id = :user_id';
            $params[':user_id'] = $userId;
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            $sql .= ' AND DATE(t.created_at) >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo !== null && $dateTo !== '') {
            $sql .= ' AND DATE(t.created_at) <= :date_to';
            $params[':date_to'] = $dateTo;
        }
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
