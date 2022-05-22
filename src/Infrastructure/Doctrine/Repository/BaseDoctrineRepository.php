<?php

namespace App\Infrastructure\Doctrine\Repository;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

abstract class BaseDoctrineRepository
{
    protected EntityManager $entityManager;

    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    protected function find(string $entityClass, UuidInterface $id)
    {
        return $this->entityManager->find($entityClass, $id);
    }

    protected function select(string $entityClass, string $alias): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->from($entityClass, $alias)->select($alias);
    }

    protected function from(string $entity, string $alias, string $indexBy = null): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->from($entity, $alias, $indexBy);
    }
}
