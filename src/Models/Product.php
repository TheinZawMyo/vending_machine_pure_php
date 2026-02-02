<?php

namespace Theinzawmyo\VendingMachine\Models;

/**
 * Product entity: id, name, price, quantity.
 */
class Product
{
    public int $id;
    public string $name;
    public string $price;
    public int $quantityAvailable;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    public function __construct(array $row = [])
    {
        if (!empty($row)) {
            $this->id = (int) ($row['id'] ?? 0);
            $this->name = (string) ($row['name'] ?? '');
            $this->price = (string) ($row['price'] ?? '0');
            $this->quantityAvailable = (int) ($row['quantity'] ?? 0);
            $this->createdAt = $row['created_at'] ?? null;
            $this->updatedAt = $row['updated_at'] ?? null;
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantityAvailable,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
