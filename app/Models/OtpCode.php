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

        // Important : l'expiration est calculée par MySQL (NOW() + INTERVAL),
        // pas par PHP (date()/time()). Si on la calcule en PHP, elle utilise
        // le fuseau horaire du serveur PHP, alors que verify() la compare à
        // NOW() côté MySQL — un décalage de fuseau horaire entre les deux
        // peut alors placer expire_at avant l'heure d'envoi réelle et rendre
        // le code invalide immédiatement. En laissant MySQL calculer les deux
        // valeurs (insertion et vérification), elles restent toujours
        // cohérentes entre elles, quel que soit le fuseau horaire de PHP.
        $id = Database::insert(
            "INSERT INTO otp_codes (user_id, otp_code, expire_at, ip_address, browser, device)
             VALUES (?, ?, NOW() + INTERVAL 5 MINUTE, ?, ?, ?)",
            [$userId, $code, $ipAddress, $browser, $device]
        );

        // On relit expire_at tel que MySQL l'a réellement stocké (utile pour
        // l'affichage du compte à rebours côté vue), plutôt que de recalculer
        // une valeur en PHP qui pourrait diverger.
        $row = Database::fetch("SELECT expire_at FROM otp_codes WHERE id = ?", [$id]);

        return [
            'id' => $id,
            'code' => $code,
            'expire_at' => $row['expire_at'] ?? null,
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