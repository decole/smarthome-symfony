<?php

declare(strict_types=1);

namespace App\Domain\Sensor\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Sensor\SensorCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;

final class SensorCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private readonly SensorRepositoryInterface $repository,
        private readonly SensorCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    final public function getRepository(): SensorRepositoryInterface
    {
        return $this->repository;
    }

    final public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}