<?php

declare(strict_types=1);

namespace App\Domain\Relay\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Relay\RelayCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;

final class RelayCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private readonly RelayRepositoryInterface $repository,
        private readonly RelayCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    public function getRepository(): RelayRepositoryInterface
    {
        return $this->repository;
    }

    public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}