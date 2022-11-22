<?php

namespace App\Application\Service\DeviceData;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Application\Service\Factory\DeviceDataValidationFactory;
use App\Domain\Payload\DevicePayload;
use Psr\Cache\InvalidArgumentException;

final class DataValidationService
{
    public function __construct(private DeviceCacheService $deviceCacheService)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws DeviceDataException
     */
    public function validate(DevicePayload $payload): DeviceDataValidatedDto
    {
        $validator = (new DeviceDataValidationFactory($this->deviceCacheService->getTopicMapByDeviceTopic()))
            ->create($payload);

        return $validator->validate();
    }
}