<?php

namespace Theinzawmyo\VendingMachine\Controllers;

use Theinzawmyo\VendingMachine\Auth\SessionAuth;
use Theinzawmyo\VendingMachine\Repositories\UserRepository;
use Theinzawmyo\VendingMachine\Validation\UserValidator;

class AuthController
{
    private SessionAuth $auth;
    private UserRepository $userRepository;
    private UserValidator $userValidator;

    public function __construct(
        ?SessionAuth $auth = null,
        ?UserRepository $userRepository = null,
        ?UserValidator $userValidator = null
    ) {
        $this->auth = $auth ?? new SessionAuth();
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->userValidator = $userValidator ?? new UserValidator();
    }

    public function loginForm(): void
    {
        $error = '';
        $basePath = __DIR__ . '/../../views';
        require $basePath . '/auth/login.php';
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '' || $password === '') {
            $error = 'Username and password are required.';
            $basePath = __DIR__ . '/../../views';
            require $basePath . '/auth/login.php';
            return;
        }
        if (!$this->auth->login($username, $password)) {
            $error = 'Invalid username or password.';
            $basePath = __DIR__ . '/../../views';
            require $basePath . '/auth/login.php';
            return;
        }
        header('Location: /products');
        exit;
    }

    public function registerForm(): void
    {
        $errors = [];
        $basePath = __DIR__ . '/../../views';
        require $basePath . '/auth/register.php';
    }

    public function register(): void
    {
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
        ];
        if (!$this->userValidator->validateRegister($data)) {
            $errors = $this->userValidator->getErrors();
            $basePath = __DIR__ . '/../../views';
            require $basePath . '/auth/register.php';
            return;
        }
        $this->userRepository->create([
            'username' => $data['username'],
            'password' => $data['password'],
            'role' => 'user',
        ]);
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        $this->auth->logout();
        header('Location: /login');
        exit;
    }
}
