<?php
namespace App\Models;

use App\Helpers\Database;

class Notification
{
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM notifications WHERE id = ?", [$id]);
    }

    public static function findByUserId(int $userId, int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO notifications (user_id, type, title, body, link) VALUES (?, ?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['type'] ?? 'general',
                $data['title'],
                $data['body'] ?? null,
                $data['link'] ?? null,
            ]
        );
    }

    public static function markRead(int $id): void
    {
        Database::execute("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ?", [$id]);
    }

    public static function markAllRead(int $userId): void
    {
        Database::execute("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0", [$userId]);
    }

    public static function unreadCount(int $userId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0", [$userId]);
        return $row ? (int)$row['count'] : 0;
    }

    public static function createForAllUsers(array $data): int
    {
        $users = Database::fetchAll("SELECT id FROM users WHERE is_active = 1 AND (notify_push = 1 OR notify_push IS NULL) AND role_id != (SELECT id FROM roles WHERE slug IN ('super_admin','admin') LIMIT 1)");
        $count = 0;
        foreach ($users as $user) {
            self::create([
                'user_id' => $user['id'],
                'type'    => $data['type'] ?? 'general',
                'title'   => $data['title'],
                'body'    => $data['body'] ?? null,
                'link'    => $data['link'] ?? null,
            ]);
            $count++;
        }
        return $count;
    }
}
