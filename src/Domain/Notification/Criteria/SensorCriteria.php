<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

use App\Domain\Sensor\Entity\Sensor;

final class SensorCriteria extends AbstractCriteria
{
    public function notify(): void
    {
        /** @var Sensor $device */
        $device = $this->device;

        if ($device->isNotify()) {
            $this->sendByVisualNotify();
            $this->sendByMessengers();
        }
    }

    public function prepareAlertMessage(): string
    {
        $deviceAlertMessage = $this->device?->getStatusMessage()?->getMessageWarn();

        $name = $this->device?->getName() ?? $this->payload->getTopic();

        return $deviceAlertMessage ??
            "Внимание! Сенсор {$name} имеет неопознанное состояние [{value}] !";
    }
}