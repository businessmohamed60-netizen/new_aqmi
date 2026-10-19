<?php
namespace App\Helpers;

use App\Helpers\Database;
use App\Helpers\Session;

/**
 * BotProtection — détection et blocage des robots sur les endpoints d'authentification.
 *
 * Trois couches :
 * 1. User-Agent blacklist (curl, wget, python, bots connus)
 * 2. Honeypot (champ caché que les robots remplissent, pas les humains)
 * 3. Rate limiting par IP (max N tentatives par fenêtre de temps)
 */
class BotProtection
{
    /** Patterns User-Agent typiques des robots/scanners */
    private const BOT_PATTERNS = [
        'bot', 'crawl', 'spider', 'scrap', 'curl', 'wget', 'python-requests',
        'libwww', 'httpclient', 'httpunit', 'nikto', 'nmap', 'masscan',
        'sqlmap', 'acunetix', 'nessus', 'w3af', 'hydra', 'burp', 'zap',
        'fuzzer', 'semrush', 'ahrefs', 'dotbot', 'petalbot', 'bytespider',
        'dataforseo', 'mj12bot', 'sogou', 'exabot', 'zombie', 'headless',
        'phantomjs', 'selenium', 'puppeteer', 'mechanize', 'go-http-client',
        'okhttp', 'java/', 'scrapy', 'httpx',
    ];

    /** IP sur liste noire permanente */
    private const BLOCKED_IPS = [];

    /**
     * Vérifie si la requête provient d'un robot.
     * Retourne true si le robot est détecté, false sinon.
     */
    public static function isBot(): bool
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

        // User-Agent vide = robot
        if (trim($ua) === '') {
            return true;
        }

        // User-Agent trop court ou trop générique = robot
        if (strlen($ua) < 10) {
            return true;
        }

        // Vérifier les patterns connus
        foreach (self::BOT_PATTERNS as $pattern) {
            if (str_contains($ua, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie le champ honeypot.
     * Les robots remplissent automatiquement tous les champs ;
     * si le honeypot est rempli, c'est un robot.
     */
    public static function checkHoneypot(string $fieldName = 'website_url'): bool
    {
        $value = trim($_POST[$fieldName] ?? '');
        return $value !== '';
    }

    /**
     * Rate limiting par IP pour les tentatives d'authentification.
     * max 5 tentatives par 10 minutes, 10 par heure.
     */
    public static function checkRateLimit(string $ip, string $endpoint = 'login'): array
    {
        $recent = self::countRecentAttempts($ip, $endpoint, 10);
        if ($recent >= 5) {
            return [
                'allowed' => false,
                'message' => 'Trop de tentatives. Veuillez réessayer dans 10 minutes.',
            ];
        }

        $hourly = self::countRecentAttempts($ip, $endpoint, 60);
        if ($hourly >= 10) {
            return [
                'allowed' => false,
                'message' => 'Trop de tentatives depuis cette adresse. Veuillez réessayer dans une heure.',
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Enregistre une tentative d'authentification dans la table de rate limiting.
     */
    public static function recordAttempt(string $ip, string $endpoint, string $result): void
    {
        try {
            Database::execute(
                "INSERT INTO auth_attempts (ip_address, endpoint, result, user_agent, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$ip, $endpoint, $result, substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)]
            );
        } catch (\Throwable $e) {
            // La table peut ne pas exister encore — on ignore silencieusement
        }
    }

    /**
     * Compte les tentatives récentes pour une IP et un endpoint.
     */
    public static function countRecentAttempts(string $ip, string $endpoint, int $minutes): int
    {
        try {
            $row = Database::fetch(
                "SELECT COUNT(*) as cnt FROM auth_attempts WHERE ip_address = ? AND endpoint = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
                [$ip, $endpoint, $minutes]
            );
            return (int)($row['cnt'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Nettoie les anciennes tentatives (appeler périodiquement).
     */
    public static function cleanupOldAttempts(): void
    {
        try {
            Database::execute("DELETE FROM auth_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Vérification globale à appeler en début de doLogin / doOtp / doRegister.
     * Si un robot est détecté, retourne true (la requête doit être bloquée).
     */
    public static function guard(string $endpoint, string $honeypotField = 'website_url'): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 1. Bloquer les robots par User-Agent
        if (self::isBot()) {
            self::recordAttempt($ip, $endpoint, 'bot_blocked');
            http_response_code(403);
            return 'Accès refusé.';
        }

        // 2. Vérifier le honeypot
        if (self::checkHoneypot($honeypotField)) {
            self::recordAttempt($ip, $endpoint, 'honeypot_blocked');
            http_response_code(403);
            return 'Accès refusé.';
        }

        // 3. Rate limiting
        $rateLimit = self::checkRateLimit($ip, $endpoint);
        if (!$rateLimit['allowed']) {
            self::recordAttempt($ip, $endpoint, 'rate_limited');
            return $rateLimit['message'];
        }

        return null;
    }

    /**
     * Récupère l'IP réelle du client (en tenant compte des proxies).
     */
    public static function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
