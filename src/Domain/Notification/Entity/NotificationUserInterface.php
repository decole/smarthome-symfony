<?php

declare(strict_types=1);

namespace App\Domain\Notification\Entity;

interface NotificationUserInterface
{
    public function getTo();
}