<?php

namespace App\Domain\PLC\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\PLC\PlcCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\PlcRepositoryInterface;
use App\Domain\PLC\Service\PlcCacheService;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use App\Infrastructure\Repository\PLC\PlcRepository;

final class PlcCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private readonly PlcRepositoryInterface $repository,
        private readonly PlcCrudValidationService $validation,
        PlcCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    /**
     * @return PlcRepository
     */
    public function getRepository(): BaseDoctrineRepository
    {
        return $this->repository;
    }

    /**
     * @return PlcCrudValidationService
     */
    public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}