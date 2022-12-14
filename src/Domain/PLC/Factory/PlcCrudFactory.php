<?php

namespace App\Domain\PLC\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\PLC\PlcCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\PlcRepositoryInterface;
use App\Domain\PLC\Service\PlcCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use App\Infrastructure\Doctrine\Repository\PLC\PlcRepository;

class PlcCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private PlcRepositoryInterface $repository,
        private PlcCrudValidationService $validation,
        PlcCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    /**
     * @return PlcRepository
     */
    final public function getRepository(): BaseDoctrineRepository
    {
        return $this->repository;
    }

    /**
     * @return PlcCrudValidationService
     */
    final public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}