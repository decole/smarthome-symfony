<?php

namespace App\Domain\Notification;


interface NotificationMessageInterface
{
    public function getMessage(): string;
}