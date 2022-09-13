<?php

namespace App\Application\Service\Factory;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\Validation\DataValidation\EmptyDataValidator;
use App\Application\Service\Validation\DataValidation\FireSecurityDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\RelayDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\SecurityDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidator;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Domain\Payload\DevicePayload;

final class DeviceDataValidationFactory
{
    public function __construct(private array $map)
    {
    }

    public function create(DevicePayload $payload): DeviceDataValidatorInterface
    {
        /** @var Sensor|Relay|FireSecurity|Security $device */
        $device = $this->findDevice($payload);

        if ($device === null) {
            return new EmptyDataValidator();
        }

        return match ($device::alias()) {
            Sensor::alias() => new SensorDeviceDataValidator($payload, $device),
            Relay::alias() => new RelayDeviceDataValidator($payload, $device),
            Security::alias() => new SecurityDeviceDataValidator($payload, $device),
            FireSecurity::alias() => new FireSecurityDeviceDataValidator($payload, $device),

            default => throw DeviceDataException::notFoundValidatorType()
        };
    }

    public function findDevice(DevicePayload $payload): ?EntityInterface
    {
        foreach ($this->map as $topic => $device) {
            if ($topic === $payload->getTopic()) {
                return $device;
            }
        }

        return null;
    }
}