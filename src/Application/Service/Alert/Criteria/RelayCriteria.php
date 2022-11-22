<?php

namespace App\Application\Service\Alert\Criteria;

use App\Domain\Relay\Entity\Relay;

final class RelayCriteria extends AbstractCriteria
{
    public function notify(): void
    {
        /** @var Relay $device */
        $device = $this->device;

        if ($device->isNotify()) {
            $this->sendByVisualNotify();
            $this->sendByMessengers();
        }
    }

    public function prepareAlertMessage(): string
    {
        /** @var Relay $device */
        $deviceAlertMessage = $this->device?->getStatusMessage()?->getMessageWarn();

        $name = $this->device?->getName() ?? $this->payload->getTopic();

        return $deviceAlertMessage ??
            "Внимание! Реле {$name} имеет неопознанное состояние [{value}] !";
    }
}