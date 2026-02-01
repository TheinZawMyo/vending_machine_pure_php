<?php

namespace Theinzawmyo\VendingMachine\Auth;

use Theinzawmyo\VendingMachine\Models\User;
use Theinzawmyo\VendingMachine\Repositories\UserRepository;

/**
 * Session-based authentication with password hashing.
 * Supports roles: admin, user.
 */
class SessionAuth
{
    private const SESSION_USER_KEY = 'user';
    private const SESSION_USER_ID = 'user_id';
    private const SESSION_ROLE = 'role';

    private UserRepository $userRepository;

    public function __construct(?UserRepository $userRepository = null)
    {
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $username, string $password): bool
    {
        $this->startSession();
        $user = $this->userRepository->verifyPassword($username, $password);
        if ($user === null) {
            return false;
        }
        $_SESSION[self::SESSION_USER_ID] = $user->id;
        $_SESSION[self::SESSION_ROLE] = $user->role;
        $_SESSION[self::SESSION_USER_KEY] = $user->toArray();
        return true;
    }

    public function logout(): void
    {
        $this->startSession();
        unset(
            $_SESSION[self::SESSION_USER_ID],
            $_SESSION[self::SESSION_ROLE],
            $_SESSION[self::SESSION_USER_KEY]
        );
    }

    public function isLoggedIn(): bool
    {
        $this->startSession();
        return isset($_SESSION[self::SESSION_USER_ID]);
    }

    public function user(): ?User
    {
        $this->startSession();
        if (!isset($_SESSION[self::SESSION_USER_ID])) {
            return null;
        }
        return $this->userRepository->find((int) $_SESSION[self::SESSION_USER_ID]);
    }

    public function role(): string
    {
        $this->startSession();
        return $_SESSION[self::SESSION_ROLE] ?? 'user';
    }

    public function isAdmin(): bool
    {
        return $this->role() === 'admin';
    }
}
