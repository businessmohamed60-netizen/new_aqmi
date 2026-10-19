<?php
namespace App\Models;

use App\Helpers\Database;

class ChatMessage
{
    public static function findByConversation(int $conversationId): array
    {
        return Database::fetchAll(
            "SELECT m.*, u.firstname, u.lastname
             FROM chat_messages m
             JOIN users u ON m.sender_id = u.id
             WHERE m.conversation_id = ?
             ORDER BY m.created_at ASC",
            [$conversationId]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO chat_messages (conversation_id, sender_id, sender_type, body) VALUES (?, ?, ?, ?)",
            [$data['conversation_id'], $data['sender_id'], $data['sender_type'], $data['body']]
        );
    }

    public static function markConversationReadByAdmin(int $conversationId): void
    {
        Database::execute(
            "UPDATE chat_messages SET is_read = 1, read_at = NOW() WHERE conversation_id = ? AND sender_type = 'user' AND is_read = 0",
            [$conversationId]
        );
    }

    public static function markConversationReadByUser(int $conversationId): void
    {
        Database::execute(
            "UPDATE chat_messages SET is_read = 1, read_at = NOW() WHERE conversation_id = ? AND sender_type = 'admin' AND is_read = 0",
            [$conversationId]
        );
    }

    public static function getLastMessage(int $conversationId): ?array
    {
        return Database::fetch(
            "SELECT * FROM chat_messages WHERE conversation_id = ? ORDER BY created_at DESC LIMIT 1",
            [$conversationId]
        );
    }
}
