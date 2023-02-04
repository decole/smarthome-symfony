<?php

namespace App\Domain\DeviceData\Service;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\Factory\DeviceDataValidationFactory;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Payload\Entity\DevicePayload;
use Psr\Cache\InvalidArgumentException;

final class DeviceDataValidationService
{
    public function __construct(private readonly DeviceCacheService $deviceCacheService)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws DeviceDataException
     */
    public function execute(DevicePayload $payload): DeviceDataValidatedDto
    {
        $validator = (new DeviceDataValidationFactory($this->deviceCacheService->getTopicMapByDeviceTopic()))
            ->create($payload);

        return $validator->handle();
    }
}