<?php

return [
    'database' => [
        'host'     => getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => getenv('DB_PORT') ?: '3306',
        'dbname'   => getenv('DB_NAME') ?: 'vending_machine',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: 'root',
        'charset'  => 'utf8mb4',
    ],
    'session' => [
        'name' => 'VM_SESSION',
        'lifetime' => 3600,
    ],
    'timezone' => 'Asia/Yangon'
];
