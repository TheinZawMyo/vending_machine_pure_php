<?php

namespace Theinzawmyo\VendingMachine\Validation;

use Theinzawmyo\VendingMachine\Repositories\UserRepository;

/**
 * Validation for user registration and admin user form.
 */
class UserValidator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(private ?UserRepository $userRepository = null)
    {
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function validateRegister(array $data): bool
    {
        $this->errors = [];

        $username = trim($data['username'] ?? '');
        if ($username === '') {
            $this->errors['username'] = 'Username is required.';
        } elseif (strlen($username) < 3) {
            $this->errors['username'] = 'Username must be at least 3 characters.';
        } elseif ($this->userRepository->findByUsername($username) !== null) {
            $this->errors['username'] = 'Username is already taken.';
        }

        $password = $data['password'] ?? '';
        if ($password === '') {
            $this->errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $this->errors['password'] = 'Password must be at least 6 characters.';
        }

        if (($data['password_confirm'] ?? '') !== $password) {
            $this->errors['password_confirm'] = 'Passwords do not match.';
        }

        return empty($this->errors);
    }

    public function validateUserForm(array $data, ?int $excludeUserId = null): bool
    {
        $this->errors = [];

        $username = trim($data['username'] ?? '');
        if ($username === '') {
            $this->errors['username'] = 'Username is required.';
        } elseif (strlen($username) < 3) {
            $this->errors['username'] = 'Username must be at least 3 characters.';
        } else {
            $existing = $this->userRepository->findByUsername($username);
            if ($existing !== null && $existing->id !== $excludeUserId) {
                $this->errors['username'] = 'Username is already taken.';
            }
        }

        $password = $data['password'] ?? '';
        if ($excludeUserId === null) {
            if ($password === '') {
                $this->errors['password'] = 'Password is required.';
            } elseif (strlen($password) < 6) {
                $this->errors['password'] = 'Password must be at least 6 characters.';
            }
        } elseif ($password !== '' && strlen($password) < 6) {
            $this->errors['password'] = 'Password must be at least 6 characters if provided.';
        }

        $role = $data['role'] ?? 'user';
        if (!in_array($role, ['admin', 'user'], true)) {
            $this->errors['role'] = 'Invalid role.';
        }

        return empty($this->errors);
    }

    /** @return array<string, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
