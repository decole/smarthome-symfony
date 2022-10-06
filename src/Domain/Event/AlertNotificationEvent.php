<?php

namespace App\Domain\Event;

class AlertNotificationEvent
{
    public const NAME = 'notification.visual.send';

    public const MESSENGER = 'messenger';
    public const ALICE = 'alice';

    public function __construct(private string $message, private array $types)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTypes(): array
    {
        return $this->types;
    }
}