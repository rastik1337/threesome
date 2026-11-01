<?php

declare(strict_types=1);

namespace App\Models;

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
