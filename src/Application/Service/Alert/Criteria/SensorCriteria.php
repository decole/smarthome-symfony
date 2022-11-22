<?php

namespace App\Application\Service\Alert\Criteria;

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
        /** @var Sensor $device */
        $deviceAlertMessage = $this->device?->getStatusMessage()?->getMessageWarn();

        $name = $this->device?->getName() ?? $this->payload->getTopic();

        return $deviceAlertMessage ??
            "Внимание! Сенсор {$name} имеет неопознанное состояние [{value}] !";
    }
}