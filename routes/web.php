<?php

use Theinzawmyo\VendingMachine\Router;
use Theinzawmyo\VendingMachine\Controllers\ProductsController;
use Theinzawmyo\VendingMachine\Controllers\AuthController;
use Theinzawmyo\VendingMachine\Controllers\UsersController;
use Theinzawmyo\VendingMachine\Controllers\AdminController;
use Theinzawmyo\VendingMachine\Middleware\RequireAdmin;
use Theinzawmyo\VendingMachine\Middleware\RequireAuth;

return function (Router $router) {
    $admin = [new RequireAdmin()];
    $auth = [new RequireAuth()];

    // Root: redirect admin to dashboard, user to products, guest to login
    $router->get('/', function (array $p) {
        $auth = new \Theinzawmyo\VendingMachine\Auth\SessionAuth();
        $auth->startSession();
        if (!$auth->isLoggedIn()) {
            header('Location: /login');
            return;
        }
        header('Location: ' . ($auth->isAdmin() ? '/admin' : '/products'));
        return;
    });

    // Auth (public)
    $router->get('/login', fn(array $p) => (new AuthController())->loginForm());
    $router->post('/login', fn(array $p) => (new AuthController())->login());
    $router->get('/register', fn(array $p) => (new AuthController())->registerForm());
    $router->post('/register', fn(array $p) => (new AuthController())->register());
    $router->get('/logout', fn(array $p) => (new AuthController())->logout());

    // Products — only when logged in (static routes before /products/{id} so "create" isn't matched as id)
    $router->get('/products', fn(array $p) => (new ProductsController())->index(), $auth);
    $router->get('/products/create', fn(array $p) => (new ProductsController())->createForm(), $admin);
    $router->post('/products/create', fn(array $p) => (new ProductsController())->create(), $admin);
    $router->get('/products/{id}', fn(array $p) => (new ProductsController())->show((int) $p['id']), $auth);
    $router->get('/products/{id}/purchase', fn(array $p) => (new ProductsController())->purchase((int) $p['id']), $auth);
    $router->post('/products/{id}/purchase', fn(array $p) => (new ProductsController())->purchase((int) $p['id']), $auth);
    $router->get('/products/{id}/edit', fn(array $p) => (new ProductsController())->editForm((int) $p['id']), $admin);
    $router->post('/products/{id}/update', fn(array $p) => (new ProductsController())->update((int) $p['id']), $admin);
    $router->get('/products/{id}/delete', fn(array $p) => (new ProductsController())->delete((int) $p['id']), $admin);

    // get transactions for logged-in user
    $router->get('/transactions', fn(array $p) => (new ProductsController())->userTransactions(), $auth);

    // Admin dashboard (sidebar)
    $router->get('/admin', fn(array $p) => (new AdminController())->dashboard(), $admin);
    $router->get('/admin/inventory', fn(array $p) => (new AdminController())->inventory(), $admin);
    $router->get('/admin/transactions', fn(array $p) => (new AdminController())->transactions(), $admin);

    // Admin-only: user CRUD
    $router->get('/users', fn(array $p) => (new UsersController())->index(), $admin);
    $router->get('/users/create', fn(array $p) => (new UsersController())->createForm(), $admin);
    $router->post('/users/create', fn(array $p) => (new UsersController())->create(), $admin);
    $router->get('/users/{id}/edit', fn(array $p) => (new UsersController())->editForm((int) $p['id']), $admin);
    $router->post('/users/{id}/update', fn(array $p) => (new UsersController())->update((int) $p['id']), $admin);
    $router->get('/users/{id}/delete', fn(array $p) => (new UsersController())->delete((int) $p['id']), $admin);

    return $router;
};
