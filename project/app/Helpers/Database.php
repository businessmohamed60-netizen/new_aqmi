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
        $stmt = self::getInstance()->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
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

    // ------------------------------------------------------------------
    // Table-oriented convenience API (used by the Report Studio module).
    // These complement the raw-SQL methods above; both sets coexist.
    // ------------------------------------------------------------------

    /**
     * Fetch a single row by raw SQL. Alias kept for module compatibility.
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        return self::fetch($sql, $params);
    }

    /**
     * Insert a row from an associative array and return the new id.
     */
    public static function insertRow(string $table, array $data): int
    {
        $pdo = self::getInstance()->pdo;
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_combine($placeholders, array_values($data)));
        return (int) $pdo->lastInsertId();
    }

    /**
     * Update rows matching a where clause from an associative array.
     */
    public static function updateRow(string $table, array $data, array $where): bool
    {
        $setClauses = [];
        $params = [];
        foreach ($data as $col => $val) {
            $setClauses[] = $col . ' = :set_' . $col;
            $params[':set_' . $col] = $val;
        }
        $whereClauses = [];
        foreach ($where as $col => $val) {
            $whereClauses[] = $col . ' = :where_' . $col;
            $params[':where_' . $col] = $val;
        }
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $setClauses),
            implode(' AND ', $whereClauses)
        );
        return self::execute($sql, $params) >= 0;
    }

    /**
     * Delete rows matching a where clause from an associative array.
     */
    public static function deleteRow(string $table, array $where): bool
    {
        $whereClauses = [];
        $params = [];
        foreach ($where as $col => $val) {
            $whereClauses[] = $col . ' = :where_' . $col;
            $params[':where_' . $col] = $val;
        }
        $sql = sprintf('DELETE FROM %s WHERE %s', $table, implode(' AND ', $whereClauses));
        return self::execute($sql, $params) >= 0;
    }
}