<?php

namespace App\Domain\FireSecurity\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\FireSecurity\FireSecurityCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class FireSecurityCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private FireSecurityRepositoryInterface $repository,
        private FireSecurityCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
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