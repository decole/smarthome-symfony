<?php

declare(strict_types=1);

namespace App\Domain\VisualNotification\Entity;

use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;
use App\Infrastructure\Repository\VisualNotification\VisualNotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisualNotificationRepository::class)]
#[ORM\Table(name: 'visual_notify')]
final class VisualNotification implements EntityInterface
{
    use CreatedAt;
    use Entity;
    use UpdatedAt;

    #[ORM\Column(type: Types::BOOLEAN)]
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

    public function __construct(
        #[ORM\Column(type: Types::SMALLINT)]
        private readonly int $type,
        #[ORM\Column(type: Types::STRING, length: 500)]
        private string $message,
    ) {
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
