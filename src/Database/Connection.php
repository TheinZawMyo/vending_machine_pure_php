<?php

namespace Theinzawmyo\VendingMachine\Database;

use PDO;
use PDOException;

/**
 * Manages the PDO database connection (singleton-style).
 */
final class Connection
{
    private static ?PDO $instance = null;

    /** @var array{host:string,port:string,dbname:string,username:string,password:string,charset:string} */
    private static array $config = [];

    public static function setConfig(array $config): void
    {
        self::$config = array_merge([
            'host'     => '127.0.0.1',
            'port'     => '3306',
            'dbname'   => 'vending_machine',
            'username' => 'root',
            'password' => 'root',
            'charset'  => 'utf8mb4',
        ], $config);
    }

    /**
     * @return PDO
     * @throws PDOException
     */
    public static function get(): PDO
    {
        if (self::$instance === null) {
            $c = self::$config;
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $c['host'],
                $c['port'],
                $c['dbname'],
                $c['charset']
            );
            self::$instance = new PDO($dsn, $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$instance;
    }

    public static function setInstance(?PDO $pdo): void
    {
        self::$instance = $pdo;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
