<?php

namespace App\Domain\DeviceData\Service;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\Factory\DeviceDataValidationFactory;
use App\Domain\DeviceData\Entity\DeviceDataValidated;
use App\Domain\Payload\Entity\DevicePayload;
use Psr\Cache\InvalidArgumentException;

final class DeviceDataValidationService
{
    public function __construct(private DeviceCacheService $deviceCacheService)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws DeviceDataException
     */
    public function validate(DevicePayload $payload): DeviceDataValidated
    {
        $validator = (new DeviceDataValidationFactory($this->deviceCacheService->getTopicMapByDeviceTopic()))
            ->create($payload);

        return $validator->validate();
    }
}