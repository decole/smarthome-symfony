<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Relay\Entity\Relay;

final class RelayDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(DeviceDataValidatedDto $dto): void
    {
        \assert($this->device instanceof Relay);

        $payload = $this->payload->getPayload();

        if ($this->device->getTopic() === $this->payload->getTopic()) {
            $dto->hasAlertingNotify = $this->device->getCommandOn() !== $payload
                && $this->device->getCommandOff() !== $payload;
        }

        if ($this->device->getCheckTopic() === $this->payload->getTopic()) {
            $dto->hasAlertingNotify = $this->device->getCheckTopicPayloadOn() !== $payload
                && $this->device->getCheckTopicPayloadOff() !== $payload;
        }

        if ($this->device->getStatus() && null !== $this->device->getCheckTopic()) {
            $dto->hasCheckStatusWarning = $payload !== $this->device->getCheckTopicPayloadOn()
            && $payload !== $this->device->getCheckTopicPayloadOff();
        }
    }
}
