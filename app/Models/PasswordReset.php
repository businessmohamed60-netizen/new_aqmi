<?php
namespace App\Models;

use App\Helpers\Database;

class PasswordReset
{
    /**
     * Crée un token de réinitialisation
     */
    public static function create(int $userId, string $ipAddress = null): array
    {
        // Invalider les anciens tokens
        Database::execute(
            "UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0",
            [$userId]
        );

        $token = bin2hex(random_bytes(32));
        $expireAt = date('Y-m-d H:i:s', time() + 1800); // 30 minutes

        Database::insert(
            "INSERT INTO password_resets (user_id, token, expire_at, ip_address) VALUES (?, ?, ?, ?)",
            [$userId, $token, $expireAt, $ipAddress]
        );

        return [
            'token' => $token,
            'expire_at' => $expireAt,
        ];
    }

    /**
     * Vérifie et retourne un token valide
     */
    public static function verify(string $token): ?array
    {
        return Database::fetch(
            "SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expire_at > NOW() ORDER BY id DESC LIMIT 1",
            [$token]
        );
    }

    /**
     * Marque un token comme utilisé
     */
    public static function markUsed(int $id): void
    {
        Database::execute("UPDATE password_resets SET used = 1 WHERE id = ?", [$id]);
    }

    /**
     * Nettoie les tokens expirés
     */
    public static function cleanExpired(): int
    {
        return Database::execute("DELETE FROM password_resets WHERE expire_at < NOW()");
    }
}
