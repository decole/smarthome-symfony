<?php


namespace App\Domain\Notification;


interface NotificationInterface
{
    public function getMessage(): string;

    public function getTo(): int;
}