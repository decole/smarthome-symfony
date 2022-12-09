<?php

namespace App\Infrastructure\Doctrine\Service\Security\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Security\SecurityCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class SecurityCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private SecurityRepositoryInterface $repository,
        private SecurityCrudValidationService $validation,
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