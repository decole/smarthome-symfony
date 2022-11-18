<?php

namespace App\Infrastructure\Doctrine\Repository\Profile;

use App\Domain\Contract\Repository\ProfileRepositoryInterface;
use App\Domain\Identity\Entity\User;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;

final class ProfileRepository extends BaseDoctrineRepository implements ProfileRepositoryInterface
{
    /**
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?User
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function isExistDuplicateEmail(string $login, string $email): bool
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('count(u.email)')
            ->from(User::class, 'u')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('u.email', ':email'),
                    $qb->expr()->neq('u.name', ':login')
                )
            );

        $qb
            ->setParameter('login', $login)
            ->setParameter('email', $email);

        return $qb->getQuery()->getSingleScalarResult() !== 0;
    }
}