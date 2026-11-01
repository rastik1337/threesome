<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

class Message
{
    public readonly int $message_id;
    public int $source_id;
    public int $target_id;
    public ?string $subject;
    public string $body;
    public bool $is_read;
    public readonly DateTime $created_at;

    public function __construct(int $message_id, int $source_id, int $target_id, string|null $subject, string $body, bool $is_read, DateTime $created_at)
    {
        $this->message_id = $message_id;
        $this->source_id = $source_id;
        $this->target_id = $target_id;
        $this->subject = $subject;
        $this->body = $body;
        $this->is_read = $is_read;
        $this->created_at = $created_at;
    }
}
