<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Security\Entity\Security;

final class SecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(DeviceDataValidatedDto $dto): void
    {
        \assert($this->device instanceof Security);

        if ($this->device->isGuarded()) {
            $dto->hasAlertingNotify = match ($this->payload->getPayload()) {
                $this->device->getDetectPayload() => true,

                default => false,
            };
        }

        if ($this->device->getStatus()) {
            $dto->hasCheckStatusWarning = $this->device->getPayload() !== $this->payload->getPayload();
        }
    }
}
