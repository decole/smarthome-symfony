<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Contract\Repository\EntityInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\TransactionRequiredException;
use Ramsey\Uuid\UuidInterface;

abstract class BaseDoctrineRepository
{
    protected EntityManager $entityManager;

    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    final public function save(EntityInterface $sensor): EntityInterface
    {
        $this->entityManager->persist($sensor);
        $this->entityManager->flush($sensor);

        return $sensor;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    final public function delete(EntityInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @throws OptimisticLockException|TransactionRequiredException|ORMException
     */
    protected function find(string $entityClass, UuidInterface $id)
    {
        return $this->entityManager->find($entityClass, $id);
    }

    final protected function select(string $entityClass, string $alias): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->from($entityClass, $alias)->select($alias);
    }

    final protected function from(string $entity, string $alias, string $indexBy = null): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->from($entity, $alias, $indexBy);
    }
}
