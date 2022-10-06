<?php

namespace App\Domain\Event;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Domain\Doctrine\VisualNotification\Entity\VisualNotification;

class VisualNotificationEvent
{
    public const NAME = 'notification.visual.send';

    public function __construct(private string $message, private EntityInterface $device)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Определяет тип визуальной нотификации по типу датчика
     * @return string
     */
    public function getNotifyType(): string
    {
        return match ($this->device::alias()) {
            Sensor::alias(),
            Relay::alias() => VisualNotification::ALERT_TYPE,
            FireSecurity::alias() => VisualNotification::FIRE_SECURE_TYPE,
            Security::alias() => VisualNotification::SECURITY_TYPE,
        };
    }
}