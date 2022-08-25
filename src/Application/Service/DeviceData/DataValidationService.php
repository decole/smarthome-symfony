<?php

namespace App\Application\Service\DeviceData;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Application\Service\Factory\DeviceDataValidationFactory;
use App\Domain\Payload\DevicePayload;

final class DataValidationService
{
    public function __construct(private DeviceCacheService $deviceCacheService)
    {
    }

    public function validate(DevicePayload $payload): DeviceDataValidatedDto
    {
        $validator = (new DeviceDataValidationFactory($this->deviceCacheService->getTopicMapByDeviceType()))
            ->create($payload);

        return $validator->validate();
    }
}
