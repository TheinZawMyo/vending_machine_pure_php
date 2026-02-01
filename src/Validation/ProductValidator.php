<?php

namespace Theinzawmyo\VendingMachine\Validation;

/**
 * Server-side validation for product form.
 * Rules: all fields required, price > 0, quantity >= 0.
 */
class ProductValidator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function validate(array $data): bool
    {
        $this->errors = [];

        if (empty(trim($data['name'] ?? ''))) {
            $this->errors['name'] = 'Name is required.';
        }

        if (!isset($data['price']) || $data['price'] === '') {
            $this->errors['price'] = 'Price is required.';
        } elseif (!is_numeric($data['price']) || (float) $data['price'] <= 0) {
            $this->errors['price'] = 'Price must be a positive number.';
        }

        if (!array_key_exists('quantity', $data)) {
            $this->errors['quantity'] = 'Quantity is required.';
        } elseif (!is_numeric($data['quantity']) || (int) $data['quantity'] < 0) {
            $this->errors['quantity'] = 'Quantity must be a non-negative integer.';
        }

        return empty($this->errors);
    }

    /** @return array<string, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
