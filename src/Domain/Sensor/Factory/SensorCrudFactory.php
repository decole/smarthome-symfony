<?php

namespace App\Domain\Sensor\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Sensor\SensorCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class SensorCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private SensorRepositoryInterface $repository,
        private SensorCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    final public function getRepository(): BaseDoctrineRepository
    {
        return $this->repository;
    }

    final public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}