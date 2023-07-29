<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Security;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Security\Entity\Security;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;

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
            if (EntityStatusEnum::tryFrom($status) === null) {
                throw UnresolvableArgumentException::argumentIsNotSet('Security device status');
            }

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