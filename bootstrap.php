<?php

require __DIR__ . '/vendor/autoload.php';

$configPath = __DIR__ . '/configs/config.php';
if (is_file($configPath)) {
    $config = require $configPath;
    if (!empty($config['database'])) {
        \Theinzawmyo\VendingMachine\Database\Connection::setConfig($config['database']);
    }
}
