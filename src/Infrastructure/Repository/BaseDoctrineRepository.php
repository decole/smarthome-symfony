<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;

abstract class BaseDoctrineRepository extends ServiceEntityRepository
{
    /**
     * @throws OptimisticLockException|ORMException
     */
    final public function save(EntityInterface $entity): EntityInterface
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    final public function delete(EntityInterface $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    final protected function select(string $entityClass, string $alias): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()->from($entityClass, $alias)->select($alias);
    }

    final protected function from(string $entity, string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()->from($entity, $alias, $indexBy);
    }
}
