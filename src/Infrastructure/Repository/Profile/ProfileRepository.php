<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Profile;

use App\Domain\Contract\Repository\ProfileRepositoryInterface;
use App\Domain\Identity\Entity\User;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<User>
 */
final class ProfileRepository extends BaseDoctrineRepository implements ProfileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?User
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function isExistDuplicateEmail(string $login, string $email): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('count(u.email)')
            ->from(User::class, 'u')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('u.email', ':email'),
                    $qb->expr()->neq('u.name', ':login'),
                ),
            );

        $qb
            ->setParameter('login', $login)
            ->setParameter('email', $email);

        return 0 !== $qb->getQuery()->getSingleScalarResult();
    }
}
