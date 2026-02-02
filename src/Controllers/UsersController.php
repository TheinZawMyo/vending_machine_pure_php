<?php

namespace Theinzawmyo\VendingMachine\Controllers;

use Theinzawmyo\VendingMachine\Auth\SessionAuth;
use Theinzawmyo\VendingMachine\Repositories\UserRepository;
use Theinzawmyo\VendingMachine\Validation\UserValidator;

/**
 * Admin-only user CRUD.
 */
class UsersController
{
    private UserRepository $userRepository;
    private SessionAuth $auth;
    private UserValidator $userValidator;

    public function __construct(
        ?UserRepository $userRepository = null,
        ?SessionAuth $auth = null,
        ?UserValidator $userValidator = null
    ) {
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->auth = $auth ?? new SessionAuth();
        $this->userValidator = $userValidator ?? new UserValidator();
    }

    public function index(): void
    {
        $users = $this->userRepository->findAll();
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        $isAdmin = $user && $user->isAdmin();
        require $basePath . '/users/index.php';
    }

    public function createForm(): void
    {
        $userModel = null;
        $errors = [];
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        require $basePath . '/users/form.php';
    }

    public function create(): void
    {
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'user',
        ];
        if (!$this->userValidator->validateUserForm($data, null)) {
            $errors = $this->userValidator->getErrors();
            $userModel = new \Theinzawmyo\VendingMachine\Models\User();
            $userModel->id = 0;
            $userModel->username = $data['username'];
            $userModel->role = $data['role'];
            $basePath = __DIR__ . '/../../views';
            $user = $this->auth->user();
            require $basePath . '/users/form.php';
            return;
        }
        $this->userRepository->create($data);
        header('Location: /users');
        return;
    }

    public function editForm(int $id): void
    {
        $userModel = $this->userRepository->find($id);
        if (!$userModel) {
            http_response_code(404);
            echo 'User not found.';
            return;
        }
        $errors = [];
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        require $basePath . '/users/form.php';
    }

    public function update(int $id): void
    {
        $userModel = $this->userRepository->find($id);
        if (!$userModel) {
            http_response_code(404);
            echo 'User not found.';
            return;
        }
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'user',
        ];
        if (!$this->userValidator->validateUserForm($data, $id)) {
            $errors = $this->userValidator->getErrors();
            $userModel->username = $data['username'];
            $userModel->role = $data['role'];
            $basePath = __DIR__ . '/../../views';
            $user = $this->auth->user();
            require $basePath . '/users/form.php';
            return;
        }
        $this->userRepository->update($id, $data);
        header('Location: /users');
        return;
    }

    public function delete(int $id): void
    {
        $userModel = $this->userRepository->find($id);
        if (!$userModel) {
            http_response_code(404);
            echo 'User not found.';
            return;
        }
        $this->userRepository->delete($id);
        header('Location: /users');
        return;
    }
}
