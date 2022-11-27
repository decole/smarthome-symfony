<?php

namespace App\Domain\Notification\Entity;


interface NotificationMessageInterface
{
    public function getMessage(): string;
}