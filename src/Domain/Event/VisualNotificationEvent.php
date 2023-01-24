<?php

namespace App\Domain\Event;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\PLC\Entity\PLC;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\VisualNotification\Entity\VisualNotification;
use Symfony\Contracts\EventDispatcher\Event;

class VisualNotificationEvent extends Event
{
    public const NAME = 'notification.visual.send';

    public function __construct(private readonly string $message, private readonly EntityInterface $device)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Определяет тип визуальной нотификации по типу датчика
     *
     * @return string
     */
    public function getNotifyType(): string
    {
        return match ($this->device::alias()) {
            Sensor::alias(),
            Relay::alias(),
            PLC::alias() => VisualNotification::ALERT_TYPE,
            FireSecurity::alias() => VisualNotification::FIRE_SECURE_TYPE,
            Security::alias() => VisualNotification::SECURITY_TYPE,
        };
    }
}