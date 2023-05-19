<?php

namespace App\Domain\Profile\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Profile\ProfileCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\ProfileRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class ProfileCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private readonly ProfileRepositoryInterface $repository,
        private readonly ProfileCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    public function getRepository(): ProfileRepositoryInterface
    {
        return $this->repository;
    }

    public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}