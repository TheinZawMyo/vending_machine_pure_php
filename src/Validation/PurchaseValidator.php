<?php

namespace Theinzawmyo\VendingMachine\Validation;

/**
 * Validation for purchase: quantity required and positive, product must exist and have stock.
 */
class PurchaseValidator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function validate(array $data, ?int $maxQuantity = null): bool
    {
        $this->errors = [];

        if (!isset($data['quantity']) || $data['quantity'] === '') {
            $this->errors['quantity'] = 'Quantity is required.';
        } elseif (!is_numeric($data['quantity']) || (int) $data['quantity'] < 1) {
            $this->errors['quantity'] = 'Quantity must be at least 1.';
        } elseif ($maxQuantity !== null && (int) $data['quantity'] > $maxQuantity) {
            $this->errors['quantity'] = "Quantity cannot exceed available stock ({$maxQuantity}).";
        }

        return empty($this->errors);
    }

    /** @return array<string, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
