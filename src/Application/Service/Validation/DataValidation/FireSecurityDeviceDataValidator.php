<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\FireSecurity\Entity\FireSecurity;

final class FireSecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(DeviceDataValidatedDto $dto): void
    {
        \assert($this->device instanceof FireSecurity);

        $payload = $this->payload->getPayload();

        $dto->hasAlertingNotify = $this->device->getAlertPayload() === $payload;

        if ($this->device->getStatus()) {
            $dto->hasCheckStatusWarning = $payload !== $this->device->getAlertPayload()
                && $payload !== $this->device->getNormalPayload();
        }
    }
}
