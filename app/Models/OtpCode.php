<?php
namespace App\Models;

use App\Helpers\Database;

class OtpCode
{
    /**
     * Crée un code OTP pour un utilisateur
     */
    public static function create(int $userId, string $ipAddress = null, string $browser = null, string $device = null): array
    {
        // Invalider les anciens codes
        Database::execute(
            "UPDATE otp_codes SET used = 1 WHERE user_id = ? AND used = 0",
            [$userId]
        );

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expireAt = date('Y-m-d H:i:s', time() + 300); // 5 minutes

        $id = Database::insert(
            "INSERT INTO otp_codes (user_id, otp_code, expire_at, ip_address, browser, device) VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $code, $expireAt, $ipAddress, $browser, $device]
        );

        return [
            'id' => $id,
            'code' => $code,
            'expire_at' => $expireAt,
        ];
    }

    /**
     * Vérifie un code OTP
     */
    public static function verify(int $userId, string $code): array
    {
        $otp = Database::fetch(
            "SELECT * FROM otp_codes WHERE user_id = ? AND otp_code = ? AND used = 0 AND expire_at > NOW() ORDER BY id DESC LIMIT 1",
            [$userId, $code]
        );

        if (!$otp) {
            return ['valid' => false, 'message' => 'Code invalide ou expiré.'];
        }

        // Incrémenter les tentatives
        Database::execute(
            "UPDATE otp_codes SET attempts = attempts + 1 WHERE id = ?",
            [$otp['id']]
        );

        // Vérifier le nombre de tentatives
        if ($otp['attempts'] >= 5) {
            Database::execute("UPDATE otp_codes SET used = 1 WHERE id = ?", [$otp['id']]);
            return ['valid' => false, 'message' => 'Trop de tentatives. Veuillez demander un nouveau code.'];
        }

        // Marquer comme utilisé
        Database::execute("UPDATE otp_codes SET used = 1, attempts = attempts + 1 WHERE id = ?", [$otp['id']]);

        return ['valid' => true, 'message' => 'Code vérifié avec succès.'];
    }

    /**
     * Nettoie les codes expirés
     */
    public static function cleanExpired(): int
    {
        return Database::execute(
            "DELETE FROM otp_codes WHERE expire_at < NOW()"
        );
    }
}
