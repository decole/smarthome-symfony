<?php

namespace App\Infrastructure\Doctrine\Repository\FireSecurity;

use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;
use Webmozart\Assert\Assert;

final class FireSecurityRepository extends BaseDoctrineRepository implements FireSecurityRepositoryInterface
{
    public function findAll(?int $status = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('f')
            ->from(FireSecurity::class, 'f')
            ->orderBy('f.createdAt', 'DESC');

        if ($status !== null) {
            Assert::inArray($status, FireSecurity::STATUS_MAP);

            $qb
                ->where(
                    $qb->expr()->eq('f.status', ':status')
                )
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?FireSecurity
    {
        return $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from(FireSecurity::class, 'f')
            ->where('f.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $value): ?FireSecurity
    {
        return $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from(FireSecurity::class, 'f')
            ->where('f.name = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByTopic(string $value): ?FireSecurity
    {
        return $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from(FireSecurity::class, 'f')
            ->where('f.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}