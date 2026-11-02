<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Encryption;
use DateTime;

class MessageRepo
{
    public static function create(array $message)
    {
        Database::getStatement(
            "INSERT INTO messages (source_id, target_id, subject, body)
            VALUES (:source_id, :target_id, :subject, :body)",
            $message,
        )->fetch();
    }

    public static function getOutgoingAll(int $userId): array
    {
        return Database::getStatement(
            "SELECT * FROM messages WHERE source_id = :id ORDER BY created_at DESC",
            ["id" => $userId],
        )->fetchAll();
    }

    public static function getIncomingAll(int $userId): array
    {
        return Database::getStatement(
            "SELECT * FROM messages WHERE target_id = :id ORDER BY created_at DESC",
            ["id" => $userId],
        )->fetchAll();
    }

    public static function getIncomingUnreadCount(int $userId): int
    {
        $arr = Database::getStatement(
            "SELECT COUNT(*) AS cnt FROM messages WHERE target_id = :user_id AND is_read = FALSE",
            ["user_id" => $userId],
        )->fetch();

        return intval($arr['cnt'] ?? 0);
    }

    public static function getById(int $messageId): ?Message
    {
        $arr = Database::getStatement(
            "SELECT * FROM messages WHERE message_id = :message_id",
            ['message_id' => $messageId],
        )->fetch();

        if (!$arr) {
            return null;
        }

        return new Message(
            message_id: intval($arr['message_id']),
            source_id: intval($arr["source_id"]),
            target_id: intval($arr["target_id"]),
            subject: $arr['subject'] != null ? Encryption::decrypt($arr['subject']) : null,
            body: Encryption::decrypt($arr['body']),
            is_read: (bool) $arr["is_read"],
            created_at: new DateTime($arr['created_at']),
        );
    }

    public static function setIsRead(int $messageId, int $userId): void
    {
        Database::getStatement(
            "UPDATE messages SET is_read = TRUE WHERE message_id = :message_id AND target_id = :user_id",
            ["message_id" => $messageId, "user_id" => $userId],
        );
    }
}
