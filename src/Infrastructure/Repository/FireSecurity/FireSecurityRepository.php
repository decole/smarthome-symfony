<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\FireSecurity;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<FireSecurity>
 */
final class FireSecurityRepository extends BaseDoctrineRepository implements FireSecurityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FireSecurity::class);
    }

    /**
     * @throws UnresolvableArgumentException
     *
     * @return FireSecurity[]
     */
    public function findAll(?int $status = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('f')
            ->from(FireSecurity::class, 'f')
            ->orderBy('f.createdAt', 'DESC');

        if (null !== $status) {
            if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
                throw UnresolvableArgumentException::argumentIsNotSet('Fire security device status');
            }

            $qb
                ->where(
                    $qb->expr()->eq('f.status', ':status'),
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
        return $this->getEntityManager()->createQueryBuilder()
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
        return $this->getEntityManager()->createQueryBuilder()
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
        return $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from(FireSecurity::class, 'f')
            ->where('f.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
