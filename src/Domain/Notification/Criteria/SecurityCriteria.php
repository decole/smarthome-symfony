<?php

namespace App\Domain\Notification\Criteria;

use App\Domain\Security\Entity\Security;
use App\Domain\Security\Event\MqttSecurityAlertEvent;

final class SecurityCriteria extends AbstractCriteria
{
    public function notify(): void
    {
        /** @var Security $device */
        $device = $this->device;

        if ($device->isNotify() && $device->isGuarded()) {
            $this->sendByVisualNotify();
            $this->sendByMessengers();

            $this->eventDispatcher->dispatch(event: new MqttSecurityAlertEvent($device, $this->payload));
        }
    }

    public function prepareAlertMessage(): string
    {
        /** @var Security $device */
        $deviceAlertMessage = $this->device?->getStatusMessage()?->getMessageWarn();

        $name = $this->device?->getName() ?? $this->payload->getTopic();

        return $deviceAlertMessage ??
            "Внимание! Охранный датчик {$name} сработал. Состояние [{value}] !";
    }
}