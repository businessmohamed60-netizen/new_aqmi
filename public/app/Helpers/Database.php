<?php
namespace App\Helpers;

use PDO;
use PDOException;

/**
 * Database helper backed by PDO/MySQL.
 *
 * Connects to the cPanel/MySQL database using credentials from
 * app/Config/database.php or environment variables.
 * Gracefully degrades when the DB is unreachable so pages still render.
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private static int $lastInsertId = 0;
    private static bool $connectionFailed = false;

    private function __construct()
    {
        $config = require __DIR__ . '/../Config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset'] ?? 'utf8mb4'
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            self::$connectionFailed = true;
            $this->pdo = null;
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO
    {
        return self::getInstance()->pdo;
    }

    public static function isConnected(): bool
    {
        return !self::$connectionFailed && self::getInstance()->pdo !== null;
    }

    // ------------------------------------------------------------------
    // Static convenience API (matches the old interface used by Models)
    // ------------------------------------------------------------------

    public static function query(string $sql, array $params = []): \PDOStatement|false
    {
        $pdo = self::getInstance()->pdo;
        if ($pdo === null) {
            return false;
        }
        try {
            $stmt = $pdo->prepare($sql);
            self::bindParams($stmt, $params);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query failed: ' . $e->getMessage() . ' | SQL: ' . $sql);
            return false;
        }
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        if ($stmt === false) return null;
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        if ($stmt === false) return [];
        return $stmt->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        return self::fetch($sql, $params);
    }

    public static function insert(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        if ($stmt === false) return 0;
        $pdo = self::getInstance()->pdo;
        if ($pdo !== null) {
            self::$lastInsertId = (int) $pdo->lastInsertId();
        }
        // On doit retourner l'ID auto-incrémenté généré par l'INSERT, pas
        // le nombre de lignes affectées (rowCount() vaut toujours 1 pour un
        // INSERT réussi). Sans ce correctif, tout appelant qui capture le
        // résultat de Database::insert() comme un ID (ex: Lead::create(),
        // LeadCustomField::create()) recevait systématiquement 1 au lieu du
        // vrai ID — ce qui provoquait ensuite des violations de contrainte
        // de clé étrangère dès que l'ID réel n'était pas 1 (ex: Report::
        // create() avec lead_id=1 alors que le lead réel a un autre id).
        return self::$lastInsertId;
    }

    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        if ($stmt === false) return 0;
        return $stmt->rowCount();
    }

    public static function getLastInsertId(): int
    {
        return self::$lastInsertId;
    }

    public static function insertRow(string $table, array $data): int
    {
        $pdo = self::getInstance()->pdo;
        if ($pdo === null) return 0;
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $cols);
        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table,
            implode('`, `', $cols),
            implode(', ', $placeholders)
        );
        $params = array_combine($placeholders, array_values($data));
        if (self::query($sql, $params) === false) return 0;
        self::$lastInsertId = (int) $pdo->lastInsertId();
        return self::$lastInsertId;
    }

    public static function updateRow(string $table, array $data, array $where): bool
    {
        if (self::getInstance()->pdo === null) return false;
        $sets = [];
        $params = [];
        foreach ($data as $col => $val) {
            $sets[] = "`$col` = :set_$col";
            $params[":set_$col"] = $val;
        }
        $whereClauses = [];
        foreach ($where as $col => $val) {
            $whereClauses[] = "`$col` = :where_$col";
            $params[":where_$col"] = $val;
        }
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            implode(' AND ', $whereClauses)
        );
        $stmt = self::query($sql, $params);
        return $stmt !== false && $stmt->rowCount() > 0;
    }

    public static function deleteRow(string $table, array $where): bool
    {
        if (self::getInstance()->pdo === null) return false;
        $whereClauses = [];
        $params = [];
        foreach ($where as $col => $val) {
            $whereClauses[] = "`$col` = :$col";
            $params[":$col"] = $val;
        }
        $sql = sprintf('DELETE FROM `%s` WHERE %s', $table, implode(' AND ', $whereClauses));
        $stmt = self::query($sql, $params);
        return $stmt !== false && $stmt->rowCount() > 0;
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private static function bindParams(\PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $stmt->bindValue($key + 1, $value, self::pdoType($value));
            } elseif (is_string($key) && str_starts_with($key, ':')) {
                $stmt->bindValue($key, $value, self::pdoType($value));
            } elseif (is_string($key)) {
                $stmt->bindValue(':' . $key, $value, self::pdoType($value));
            }
        }
    }

    private static function pdoType(mixed $value): int
    {
        return match (true) {
            is_int($value)  => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value)  => PDO::PARAM_NULL,
            default         => PDO::PARAM_STR,
        };
    }
}