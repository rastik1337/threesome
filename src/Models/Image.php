<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

class Image
{
    public readonly int $image_id;
    public readonly int $user_id;
    public string $file_path;
    public ?string $description;
    public readonly DateTime $created_at;
    public readonly ?string $author_login;
    public readonly ?string $author_avatar;

    public function __construct(
        int $image_id,
        int $user_id,
        string $file_path,
        ?string $description,
        DateTime $created_at,
        ?string $author_login = null,
        ?string $author_avatar = null
    ) {
        $this->image_id = $image_id;
        $this->user_id = $user_id;
        $this->file_path = $file_path;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->author_login = $author_login;
        $this->author_avatar = $author_avatar;
    }
}
