<?php

declare(strict_types=1);

namespace App\Domain\FireSecurity\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\FireSecurity\FireSecurityCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;

final class FireSecurityCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private readonly FireSecurityRepositoryInterface $repository,
        private readonly FireSecurityCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    public function getRepository(): FireSecurityRepositoryInterface
    {
        return $this->repository;
    }

    public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}