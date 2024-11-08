<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Sensor;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;

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
            if (!EntityStatusEnum::tryFrom($status) instanceof \App\Domain\Common\Enum\EntityStatusEnum) {
                throw UnresolvableArgumentException::argumentIsNotSet('Sensor status');
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