<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

class User
{
    public readonly int $user_id;
    public string $login;
    public string $password;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone;
    public Gender $gender;
    public Role $role;
    public readonly DateTime $created_at;

    public function __construct(int $id, string $login, string $password, string $first_name, string $last_name, string $email, string $phone, Gender $gender, Role $role, DateTime $created_at)
    {
        $this->user_id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->gender = $gender;
        $this->role = $role;
        $this->created_at = $created_at;
    }
}

enum Gender: string
{
    case Male = "male";
    case Female = "female";
    case Other = "other";

    public function translate(): string
    {
        return match ($this) {
            self::Male => "Muž",
            self::Female => "Žena",
            self::Other => "Jiné",
        };
    }
}

enum Role: string
{
    case User = "user";
    case Admin = "admin";

    public function translate(): string
    {
        return match ($this) {
            self::User => "Uživatel",
            self::Admin => "Správce",
        };
    }
}
