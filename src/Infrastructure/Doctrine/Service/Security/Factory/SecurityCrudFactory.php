<?php


namespace App\Infrastructure\Doctrine\Service\Security\Factory;


use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Security\SecurityValidationService;
use App\Application\Service\Validation\ValidationInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class SecurityCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private SecurityRepositoryInterface $repository,
        private SecurityValidationService $validation
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
