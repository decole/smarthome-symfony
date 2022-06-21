<?php


namespace App\Application\Service\Factory;


use App\Application\Service\Validation\ValidationInterface;
use App\Infrastructure\Doctrine\Interfaces\EntityInterface;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractCrudFactory
{
    abstract public function getRepository(): BaseDoctrineRepository;

    abstract public function getValidationService(): ValidationInterface;

    final public function validate(bool $isUpdate = false): ConstraintViolationListInterface
    {
        return $this->getValidationService()->validate($isUpdate);
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
        return $this->getRepository()->save($entity);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    final public function delete(EntityInterface $entity): void
    {
        $this->getRepository()->delete($entity);
    }
}