<?php
namespace App\Helpers;

use App\Models\User;

class Auth
{
    private static ?array $currentUser = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);
        if (!$user || !$user['is_active']) return false;

        if ($user && Security::verifyPassword($password, $user['password'])) {
            Session::set('user_id', $user['id']);
            Session::set('user_role', $user['role_id']);
            self::$currentUser = $user;

            Database::execute(
                "INSERT INTO login_logs (user_id, email, ip_address, status) VALUES (?, ?, ?, 'success')",
                [$user['id'], $email, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']
            );

            Database::execute("UPDATE users SET last_login_at = NOW() WHERE id = ?", [$user['id']]);
            return true;
        }

        Database::execute(
            "INSERT INTO login_logs (email, ip_address, status) VALUES (?, ?, 'failed')",
            [$email, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']
        );

        return false;
    }

    public static function user(): ?array
    {
        if (self::$currentUser === null) {
            $userId = Session::get('user_id');
            if ($userId) {
                self::$currentUser = User::find($userId);
            }
        }
        return self::$currentUser;
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        if (!$user) return false;
        $role = Database::fetch("SELECT slug FROM roles WHERE id = ?", [$user['role_id']]);
        return $role && in_array($role['slug'], ['super_admin', 'admin']);
    }

    public static function hasPermission(string $permission): bool
    {
        $user = self::user();
        if (!$user) return false;
        $role = Database::fetch("SELECT slug FROM roles WHERE id = ?", [$user['role_id']]);
        if ($role && $role['slug'] === 'super_admin') return true;

        $perm = Database::fetch(
            "SELECT p.id FROM permissions p JOIN role_permission rp ON p.id = rp.permission_id WHERE rp.role_id = ? AND p.slug = ?",
            [$user['role_id'], $permission]
        );
        return $perm !== null;
    }

    public static function login(string $email, string $password): bool
    {
        if (self::attempt($email, $password)) {
            $user = self::user();
            $_SESSION['user_firstname'] = $user['firstname'] ?? '';
            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        Session::destroy();
        self::$currentUser = null;
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            Session::setFlash('error', 'Veuillez vous connecter pour accéder à cette page.');
            redirect('/aqmi/login');
        }
    }

    public static function requireAdmin(): void
    {
        self::requireAuth();
        if (!self::isAdmin()) {
            Session::setFlash('error', 'Accès non autorisé.');
            redirect('/');
        }
    }
}