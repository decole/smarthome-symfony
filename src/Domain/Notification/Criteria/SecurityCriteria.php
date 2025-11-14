<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

use App\Domain\Security\Entity\Security;
use App\Domain\Security\Event\MqttSecurityAlertEvent;

final class SecurityCriteria extends AbstractCriteria
{
    public function notify(): void
    {
        /** @var Security $device */
        $device = $this->dto->device;

        if ($device->isNotify() && $device->isGuarded()) {
            $message = $this->generateAlertMessage();

            $this->sendByVisualNotify($message);
            $this->sendByMessengers($message);

            $this->eventDispatcher->dispatch(event: new MqttSecurityAlertEvent($device, $this->dto->devicePayload));
        }
    }

    public function prepareAlertMessage(): string
    {
        /** @var Security $device */
        $device = $this->dto->device;

        $text = $device->getStatusMessage()?->getMessageWarning() ?? null;

        if (empty($text)) {
            $name = $device->getName() ?? $this->dto->devicePayload->getTopic();

            $text = \sprintf('Внимание! Охранный датчик %s сработал. Состояние [{value}] !', $name);
        }

        return $text;
    }
}
