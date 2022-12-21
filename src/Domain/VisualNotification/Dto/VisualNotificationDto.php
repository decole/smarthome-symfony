<?php

namespace App\Domain\VisualNotification\Dto;

final class VisualNotificationDto
{
    public function __construct(
        private int $type,
        private string $message
    ) {
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}