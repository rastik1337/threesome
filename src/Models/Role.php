<?php

declare(strict_types=1);

namespace App\Models;

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
