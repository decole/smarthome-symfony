<?php

namespace App\Domain\Page\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Page\PageCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\PageRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class PageCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private readonly PageRepositoryInterface $repository,
        private readonly PageCrudValidationService $validation,
        DeviceCacheService $cacheService
    ) {
        $this->cacheService = $cacheService;
    }

    public function getRepository(): PageRepositoryInterface
    {
        return $this->repository;
    }

    public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}