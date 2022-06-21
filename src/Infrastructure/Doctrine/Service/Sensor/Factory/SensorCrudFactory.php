<?php


namespace App\Infrastructure\Doctrine\Service\Sensor\Factory;


use App\Application\Service\Factory\AbstractCrudFactory;
use App\Application\Service\Validation\Sensor\SensorValidationService;
use App\Application\Service\Validation\ValidationInterface;
use App\Infrastructure\Doctrine\Interfaces\SensorRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

class SensorCrudFactory extends AbstractCrudFactory
{
    public function __construct(
        private SensorRepositoryInterface $repository,
        private SensorValidationService $validation,
    ) {
    }

    final public function getRepository(): BaseDoctrineRepository
    {
        return $this->repository;
    }

    final public function getValidationService(): ValidationInterface
    {
        return $this->validation;
    }
}