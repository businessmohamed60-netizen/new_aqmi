<?php
namespace App\Helpers;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require BASE_PATH . '/app/Config/database.php';
        if (!empty($config['unix_socket']) && file_exists($config['unix_socket'])) {
            $dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']};charset={$config['charset']}";
        } else {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        }
        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        return self::getInstance()->pdo->prepare($sql);
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::getInstance()->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::getInstance()->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function insert(string $sql, array $params = []): int
    {
        $pdo = self::getInstance()->pdo;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$pdo->lastInsertId();
    }

    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::getInstance()->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function getLastInsertId(): int
    {
        return (int)self::getInstance()->pdo->lastInsertId();
    }
}