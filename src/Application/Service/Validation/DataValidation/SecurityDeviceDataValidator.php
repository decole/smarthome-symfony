<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Security\Entity\Security;

final class SecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    /**
     * @var Security $device
     */
    /**
     * null - состояние неопределено
     * true - нормальное состояние
     * false - обнаружено движение
     */
    public function handle(): DeviceDataValidatedDto
    {
        $state = match ($this->payload->getPayload()) {
            $this->device->getHoldPayload() => true,
            $this->device->getDetectPayload() => false,

            default => null,
        };

        return $this->createDto(
            state: $state,
            device: $this->device,
            isAlert: $this->payload->getPayload() === $this->device->getDetectPayload()
        );
    }
}