<?php

namespace App\Application\Service\Factory;

use App\Application\Service\DeviceData\DeviceCacheService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\EntityInterface;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractCrudFactory
{
    protected DeviceCacheService $cacheService;

    abstract public function getRepository(): BaseDoctrineRepository;

    abstract public function getValidationService(): ValidationInterface;

    final public function validate(bool $isUpdate = false): ConstraintViolationListInterface
    {
        return $this->getValidationService()->validate($isUpdate);
    }

    final public function getEntityById(string $id): ?EntityInterface
    {
        return $this->getRepository()->findById($id);
    }

    final public function list(): array
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    final public function save(EntityInterface $entity): EntityInterface
    {
        $entity = $this->getRepository()->save($entity);
        $this->refreshDeviceCache();

        return $entity;
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    final public function delete(EntityInterface $entity): void
    {
        $this->getRepository()->delete($entity);
        $this->refreshDeviceCache();
    }

    final public function refreshDeviceCache(): void
    {
        $this->getCacheService()->create();
    }

    final public function getCacheService(): DeviceCacheService
    {
        return $this->cacheService;
    }
}
