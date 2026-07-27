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
            http_response_code(503);
            die('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Maintenance - NOVAQYS</title><style>body{font-family:sans-serif;background:#0a0a0f;color:#f1f5f9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{text-align:center;max-width:500px;padding:40px}.box h1{font-size:2rem;color:#3b82f6;margin-bottom:16px}.box p{color:#94a3b8;line-height:1.6}.box a{display:inline-block;margin-top:24px;padding:12px 32px;background:#1a56db;color:#fff;text-decoration:none;border-radius:8px}</style></head><body><div class="box"><h1>Service temporairement indisponible</h1><p>La base de données est en cours de configuration. Si le problème persiste, veuillez contacter l\'administrateur.</p><a href="/">Retour à l\'accueil</a></div></body></html>');
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