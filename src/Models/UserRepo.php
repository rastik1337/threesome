<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Encryption;
use DateTime;

class UserRepo
{
    public static function create(array $user)
    {
        Database::getStatement(
            User::class,
            "INSERT INTO users (login, password, first_name, last_name, email, phone, gender, avatar_path)
            VALUES (:login, :password, :first_name, :last_name, :email, :phone, :gender, :avatar_path)",
            $user,
        )->fetch();
    }

    public static function getByLogin(string $login): ?User
    {
        $arr = Database::getStatement(
            User::class,
            "SELECT * FROM users WHERE login = :login",
            ['login' => $login],
        )->fetch();

        if (!$arr) {
            return null;
        }

        return new User(
            id: intval($arr['user_id']),
            login: $arr['login'],
            password: $arr['password'],
            first_name: Encryption::decrypt($arr['first_name']),
            last_name: Encryption::decrypt($arr['last_name']),
            email: Encryption::decrypt($arr['email']),
            phone: Encryption::decrypt($arr['phone']),
            gender: Gender::from($arr['gender']),
            role: Role::from($arr['role']),
            created_at: new DateTime($arr['created_at']),
            avatar_path: $arr['avatar_path'] ?? null,
        );
    }
}
