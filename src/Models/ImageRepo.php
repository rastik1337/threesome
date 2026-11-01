<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateTime;

class ImageRepo
{
    public static function create(int $userId, string $filePath, ?string $description = null): void
    {
        Database::execute(
            "INSERT INTO images (user_id, file_path, description) VALUES (:user_id, :file_path, :description)",
            [
                'user_id' => $userId,
                'file_path' => $filePath,
                'description' => $description,
            ]
        );
    }

    /**
     * @return Image[]
     */
    public static function getAllWithAuthor(): array
    {
        $rows = Database::fetchAll(
            "SELECT i.*, u.login AS author_login, u.avatar_path AS author_avatar
             FROM images i
             JOIN users u ON i.user_id = u.user_id
             ORDER BY i.created_at DESC"
        );

        $images = [];
        foreach ($rows as $row) {
            $images[] = new Image(
                image_id: intval($row['image_id']),
                user_id: intval($row['user_id']),
                file_path: $row['file_path'],
                description: $row['description'],
                created_at: new DateTime($row['created_at']),
                author_login: $row['author_login'] ?? null,
                author_avatar: $row['author_avatar'] ?? null
            );
        }

        return $images;
    }

    public static function getById(int $id): ?Image
    {
        $row = Database::getStatement(
            Image::class,
            "SELECT i.*, u.login AS author_login, u.avatar_path AS author_avatar
             FROM images i
             JOIN users u ON i.user_id = u.user_id
             WHERE i.image_id = :id",
            ['id' => $id]
        )->fetch();

        if (!$row) {
            return null;
        }

        return new Image(
            image_id: intval($row['image_id']),
            user_id: intval($row['user_id']),
            file_path: $row['file_path'],
            description: $row['description'],
            created_at: new DateTime($row['created_at']),
            author_login: $row['author_login'] ?? null,
            author_avatar: $row['author_avatar'] ?? null
        );
    }

    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM images WHERE image_id = :id", ['id' => $id]);
    }
}
