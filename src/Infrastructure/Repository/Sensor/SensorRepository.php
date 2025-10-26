<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Sensor;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Sensor>
 */
final class SensorRepository extends BaseDoctrineRepository implements SensorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    public function findAll(?int $status = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('s')
            ->from(Sensor::class, 's')
            ->orderBy('s.createdAt', 'DESC');

        if (null !== $status) {
            if (!EntityStatusEnum::tryFrom($status) instanceof EntityStatusEnum) {
                throw UnresolvableArgumentException::argumentIsNotSet('Sensor status');
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
    public function findById(string $id): ?Sensor
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from(Sensor::class, 's')
            ->where('s.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $value): ?Sensor
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from(Sensor::class, 's')
            ->where('s.name = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByTopic(string $value): ?Sensor
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from(Sensor::class, 's')
            ->where('s.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
