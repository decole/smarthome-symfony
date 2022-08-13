<?php


namespace App\Infrastructure\Doctrine\Service\FireSecurity\Factory;


use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\FireSecurity\FireSecurityValidationService;
use App\Application\Service\Validation\ValidationInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class FireSecurityCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private FireSecurityRepositoryInterface $repository,
        private FireSecurityValidationService $validation
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
