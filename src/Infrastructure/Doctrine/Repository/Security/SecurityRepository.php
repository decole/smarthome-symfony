<?php

namespace App\Infrastructure\Doctrine\Repository\Security;

use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;
use Webmozart\Assert\Assert;

final class SecurityRepository extends BaseDoctrineRepository implements SecurityRepositoryInterface
{
    public function findAll(?int $status = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('s')
            ->from(Security::class, 's')
            ->orderBy('s.createdAt', 'DESC');

        if ($status !== null) {
            Assert::inArray($status, Security::STATUS_MAP);

            $qb
                ->where(
                    $qb->expr()->eq('s.status', ':status')
                )
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?Security
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Security::class, 's')
            ->where('s.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $value): ?Security
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Security::class, 's')
            ->where('s.name = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByTopic(string $value): ?Security
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Security::class, 's')
            ->where('s.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
