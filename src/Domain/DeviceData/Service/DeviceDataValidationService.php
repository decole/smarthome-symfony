<?php

declare(strict_types=1);

namespace App\Domain\DeviceData\Service;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\Factory\DeviceDataValidationFactory;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Payload\Entity\DevicePayload;
use Psr\Cache\InvalidArgumentException;

final readonly class DeviceDataValidationService
{
    public function __construct(
        private DeviceCacheService $deviceCacheService,
    ) {}

    /**
     * @throws InvalidArgumentException
     * @throws DeviceDataException
     */
    public function execute(DevicePayload $payload): DeviceDataValidatedDto
    {
        return (new DeviceDataValidationFactory($this->deviceCacheService->getTopicMapByDeviceTopic()))
            ->create($payload)
            ->handle();
    }
}
