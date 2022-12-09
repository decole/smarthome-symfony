<?php

namespace App\Infrastructure\Doctrine\Service\Page\Factory;

use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Page\PageCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\PageRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class PageCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private PageRepositoryInterface $repository,
        private PageCrudValidationService $validation,
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