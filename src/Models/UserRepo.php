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
        Database::fetch(
            User::class,
            "INSERT INTO users (login, password, first_name, last_name, email, phone, gender)
            VALUES (:login, :password, :first_name, :last_name, :email, :phone, :gender)",
            $user,
        );
    }

    public static function getByLogin(string $login): ?User
    {
        $arr = Database::fetch(
            User::class,
            "SELECT * FROM users WHERE login = :login",
            ['login' => $login],
        );

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
        );
    }
}
