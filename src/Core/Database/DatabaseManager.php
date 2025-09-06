<?php

namespace App\Core\Database;

use App\Core\Config\Config;
use PDO;
use PDOException;

class DatabaseManager
{
    private ?PDO $connection = null;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    private function connect(): void
    {
        $host = $this->config->get('DB_HOST');
        $dbname = $this->config->get('DB_NAME');
        $username = $this->config->get('DB_USER');
        $password = $this->config->get('DB_PASS');

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new DatabaseException("Database connection failed", 0, $e);
        }
    }

    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    public function rollback(): bool
    {
        return $this->getConnection()->rollback();
    }

    public function prepare(string $query): \PDOStatement
    {
        return $this->getConnection()->prepare($query);
    }

    public function query(string $query): \PDOStatement
    {
        return $this->getConnection()->query($query);
    }

    public function lastInsertId(): string
    {
        return $this->getConnection()->lastInsertId();
    }
}
