<?php

declare(strict_types=1);

namespace App\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class AlertNotificationEvent extends Event
{
    public const NAME = 'notification.alert.send';

    public const MESSENGER = 'messenger';
    public const DISCORD = 'discord';
    public const ALICE = 'alice';

    /**
     * @param list<string> $types
     */
    public function __construct(private readonly string $message, private readonly array $types)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return list<string>
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}