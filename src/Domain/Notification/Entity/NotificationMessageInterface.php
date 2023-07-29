<?php

declare(strict_types=1);

namespace App\Domain\Notification\Entity;


interface NotificationMessageInterface
{
    public function getMessage(): string;
}