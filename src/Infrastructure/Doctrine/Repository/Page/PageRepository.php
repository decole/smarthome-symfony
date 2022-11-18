<?php

namespace App\Infrastructure\Doctrine\Repository\Page;

use App\Domain\Contract\Repository\PageRepositoryInterface;
use App\Domain\Page\Entity\Page;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;

final class PageRepository extends BaseDoctrineRepository implements PageRepositoryInterface
{
    public function findAll(): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('p')
            ->from(Page::class, 'p')
            ->orderBy('p.name', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $page): ?Page
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Page::class, 'p')
            ->where('p.name = :value')
            ->setParameter('value', $page)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?Page
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Page::class, 'p')
            ->where('p.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}