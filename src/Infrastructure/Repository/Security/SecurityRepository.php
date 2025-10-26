<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Security;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Security\Entity\Security;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Security>
 */
final class SecurityRepository extends BaseDoctrineRepository implements SecurityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Security::class);
    }

    public function findAll(?int $status = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('s')
            ->from(Security::class, 's')
            ->orderBy('s.createdAt', 'DESC');

        if (null !== $status) {
            if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
                throw UnresolvableArgumentException::argumentIsNotSet('Security device status');
            }

            $qb
                ->where(
                    $qb->expr()->eq('s.status', ':status'),
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
        return $this->getEntityManager()->createQueryBuilder()
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
        return $this->getEntityManager()->createQueryBuilder()
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
        return $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from(Security::class, 's')
            ->where('s.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
