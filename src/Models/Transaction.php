<?php

namespace Theinzawmyo\VendingMachine\Models;

/**
 * Transaction entity for purchase logs.
 */
class Transaction
{
    public int $id;
    public int $productId;
    public ?int $userId = null;
    public int $quantity;
    public string $totalAmount;
    public ?string $createdAt = null;

    public function __construct(array $row = [])
    {
        if (!empty($row)) {
            $this->id = (int) ($row['id'] ?? 0);
            $this->productId = (int) ($row['product_id'] ?? 0);
            $this->userId = isset($row['user_id']) ? (int) $row['user_id'] : null;
            $this->quantity = (int) ($row['quantity'] ?? 0);
            $this->totalAmount = (string) ($row['total'] ?? '0');
            $this->createdAt = $row['created_at'] ?? null;
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'user_id' => $this->userId,
            'quantity' => $this->quantity,
            'total' => $this->totalAmount,
            'created_at' => $this->createdAt,
        ];
    }
}
