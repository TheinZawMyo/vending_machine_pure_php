<?php

use Theinzawmyo\VendingMachine\Router;
use Theinzawmyo\VendingMachine\Controllers\Api\ProductsApiController;
use Theinzawmyo\VendingMachine\Controllers\Api\AuthApiController;

return function (Router $router) {
    // Token: POST /api/auth/login { "username": "...", "password": "..." }
    $router->post('/api/auth/login', fn(array $p) => (new AuthApiController())->login());
    // $routes->post('/api/auth/logout', fn(array $p) => (new AuthApiController())->logout());

    // Products
    $router->get('/api/products', fn(array $p) => (new ProductsApiController())->index());
    $router->get('/api/products/{id}', fn(array $p) => (new ProductsApiController())->show((int) $p['id']));
    // c/products/{id}', fn(array $p) => (new ProductsApiController())->delete((int) $p['id']));
    $router->post('/api/products/{id}/purchase', fn(array $p) => (new ProductsApiController())->purchase((int) $p['id']));

    return $router;
};
