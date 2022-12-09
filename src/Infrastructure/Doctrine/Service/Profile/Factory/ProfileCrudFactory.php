<?php

namespace App\Infrastructure\Doctrine\Service\Profile\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Profile\ProfileCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\ProfileRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class ProfileCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private ProfileRepositoryInterface $repository,
        private ProfileCrudValidationService $validation,
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