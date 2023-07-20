<?php

namespace App\Domain\Security\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Security\SecurityCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;

final class SecurityCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private readonly SecurityRepositoryInterface $repository,
        private readonly SecurityCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    public function getRepository(): SecurityRepositoryInterface
    {
        return $this->repository;
    }

    public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}