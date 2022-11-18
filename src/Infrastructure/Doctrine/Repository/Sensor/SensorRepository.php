<?php

namespace App\Infrastructure\Doctrine\Repository\Sensor;

use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;
use Webmozart\Assert\Assert;

final class SensorRepository extends BaseDoctrineRepository implements SensorRepositoryInterface
{
    public function findAll(?int $status = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('s')
            ->from(Sensor::class, 's')
            ->orderBy('s.createdAt', 'DESC');

        if ($status !== null) {
            Assert::inArray($status, Sensor::STATUS_MAP);

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
    public function findById(string $id): ?Sensor
    {
        return $this->entityManager->createQueryBuilder()
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
        return $this->entityManager->createQueryBuilder()
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
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Sensor::class, 's')
            ->where('s.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}