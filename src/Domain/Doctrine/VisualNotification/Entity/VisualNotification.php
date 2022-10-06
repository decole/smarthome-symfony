<?php

namespace App\Domain\Doctrine\VisualNotification\Entity;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\Common\Traits\CreatedAt;
use App\Domain\Doctrine\Common\Traits\Entity;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;

class VisualNotification implements EntityInterface
{
    private bool $isRead = false;

    public const TYPE = [
        self::MESSAGE_TYPE,
        self::ALERT_TYPE,
        self::FIRE_SECURE_TYPE,
        self::SECURITY_TYPE,
    ];

    private const STRING_TYPES = [
        self::MESSAGE_TYPE => 'notification',
        self::ALERT_TYPE => 'alert',
        self::FIRE_SECURE_TYPE => 'fire security alert',
        self::SECURITY_TYPE => 'secure alert',
    ];

    public const MESSAGE_TYPE = 0;
    public const ALERT_TYPE = 1;
    public const FIRE_SECURE_TYPE = 2;
    public const SECURITY_TYPE = 3;

    use Entity, CreatedAt, UpdatedAt;

    public function __construct(private int $type, private string $message)
    {
        $this->identify();
        $this->onCreated();
    }

    public static function alias(): string
    {
        return 'visual notification';
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function stringType(): string
    {
        return self::STRING_TYPES[$this->type];
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }
}