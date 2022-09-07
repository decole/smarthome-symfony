<?php

namespace App\Infrastructure\Doctrine\Service\Relay\Factory;

use App\Application\Service\DeviceData\DeviceCacheService;
use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Relay\RelayValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class RelayCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private RelayRepositoryInterface $repository,
        private RelayValidationService $validation,
        protected DeviceCacheService $cacheService
    ) {
    }

    public function getRepository(): BaseDoctrineRepository
    {
        return $this->repository;
    }

    public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}
