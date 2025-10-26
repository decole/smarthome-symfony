<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Relay;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Relay\Entity\Relay;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Relay>
 */
final class RelayRepository extends BaseDoctrineRepository implements RelayRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relay::class);
    }

    public function findAll(?int $status = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('r')
            ->from(Relay::class, 'r')
            ->orderBy('r.createdAt', 'DESC');

        if (null !== $status) {
            if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
                throw UnresolvableArgumentException::argumentIsNotSet('Relay device status');
            }

            $qb
                ->where(
                    $qb->expr()->eq('r.status', ':status'),
                )
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?Relay
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Relay::class, 'r')
            ->where('r.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $value): ?Relay
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Relay::class, 'r')
            ->where('r.name = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByTopic(string $value): ?Relay
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Relay::class, 'r')
            ->where('r.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
