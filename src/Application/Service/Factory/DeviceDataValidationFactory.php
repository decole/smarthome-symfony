<?php

declare(strict_types=1);

namespace App\Application\Service\Factory;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\Validation\DataValidation\FireSecurityDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\RelayDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\SecurityDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidator;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;

final readonly class DeviceDataValidationFactory
{
    public function create(EntityInterface $device, DevicePayload $payload): DeviceDataValidatorInterface
    {
        return match ($device::alias()) {
            Sensor::alias() => new SensorDeviceDataValidator($payload, $device),
            Relay::alias() => new RelayDeviceDataValidator($payload, $device),
            Security::alias() => new SecurityDeviceDataValidator($payload, $device),
            FireSecurity::alias() => new FireSecurityDeviceDataValidator($payload, $device),

            default => throw DeviceDataException::notFoundValidatorType(),
        };
    }
}
