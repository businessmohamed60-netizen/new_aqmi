<?php
namespace App\Models;

use App\Helpers\Database;

class ChatConversation
{
    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT c.*, u.firstname, u.lastname, u.email, u.company,
                    a.firstname as admin_firstname, a.lastname as admin_lastname
             FROM chat_conversations c
             JOIN users u ON c.user_id = u.id
             LEFT JOIN users a ON c.admin_id = a.id
             WHERE c.id = ?",
            [$id]
        );
    }

    public static function findByUserId(int $userId): ?array
    {
        return Database::fetch(
            "SELECT c.*, u.firstname, u.lastname, u.email, u.company,
                    a.firstname as admin_firstname, a.lastname as admin_lastname
             FROM chat_conversations c
             JOIN users u ON c.user_id = u.id
             LEFT JOIN users a ON c.admin_id = a.id
             WHERE c.user_id = ?",
            [$userId]
        );
    }

    public static function all(): array
    {
        return Database::fetchAll(
            "SELECT c.*, u.firstname, u.lastname, u.email, u.company,
                    a.firstname as admin_firstname, a.lastname as admin_lastname
             FROM chat_conversations c
             JOIN users u ON c.user_id = u.id
             LEFT JOIN users a ON c.admin_id = a.id
             ORDER BY c.last_message_at DESC"
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO chat_conversations (user_id, admin_id, subject) VALUES (?, ?, ?)",
            [$data['user_id'], $data['admin_id'] ?? null, $data['subject'] ?? null]
        );
    }

    public static function touch(int $conversationId): void
    {
        Database::execute(
            "UPDATE chat_conversations SET last_message_at = NOW() WHERE id = ?",
            [$conversationId]
        );
    }

    public static function unreadCountForUser(int $userId): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) as count FROM chat_messages m
             JOIN chat_conversations c ON m.conversation_id = c.id
             WHERE c.user_id = ? AND m.sender_type = 'admin' AND m.is_read = 0",
            [$userId]
        );
        return $row ? (int)$row['count'] : 0;
    }

    public static function unreadCountForAdmin(): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) as count FROM chat_messages WHERE sender_type = 'user' AND is_read = 0"
        );
        return $row ? (int)$row['count'] : 0;
    }
}
