<?php

use Theinzawmyo\VendingMachine\Router;
$config = require dirname(__DIR__) . '/configs/config.php';
date_default_timezone_set($config['timezone']);

require dirname(__DIR__) . '/bootstrap.php';

$router = new Router();
$web = require dirname(__DIR__) . '/routes/web.php';
$web($router);
$api = require dirname(__DIR__) . '/routes/api.php';
$api($router);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$result = $router->match($method, $uri);
if ($result !== null) {
    [$handler, $params] = $result;
    $handler($params);
} else {
    http_response_code(404);
    echo '404 Not Found';
}
