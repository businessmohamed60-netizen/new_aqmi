<?php
namespace App\Models;

use App\Helpers\Database;

class LoginHistory
{
    /**
     * Enregistre une tentative de connexion
     */
    public static function record(int $userId = null, string $email, string $result, string $ipAddress = null, string $browser = null, string $os = null): void
    {
        Database::insert(
            "INSERT INTO login_history (user_id, email, result, ip_address, browser, operating_system) VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $email, $result, $ipAddress, $browser, $os]
        );
    }

    /**
     * Récupère l'historique des connexions d'un utilisateur
     */
    public static function getByUser(int $userId, int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT * FROM login_history WHERE user_id = ? ORDER BY login_date DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    /**
     * Récupère la dernière connexion réussie (avant la plus récente)
     */
    public static function getLastSuccess(int $userId): ?array
    {
        $rows = Database::fetchAll(
            "SELECT * FROM login_history WHERE user_id = ? AND result = 'success' ORDER BY login_date DESC LIMIT 2",
            [$userId]
        );
        return $rows[1] ?? null;
    }

    /**
     * Compte les échecs récents depuis une IP
     */
    public static function countRecentFailures(string $ipAddress, int $minutes = 15): int
    {
        return (int) Database::fetch(
            "SELECT COUNT(*) as count FROM login_history WHERE ip_address = ? AND result = 'failed' AND login_date > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$ipAddress, $minutes]
        )['count'];
    }
}
