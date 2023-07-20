<?php

namespace App\Infrastructure\Repository\Relay;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Relay\Entity\Relay;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;

final class RelayRepository extends BaseDoctrineRepository implements RelayRepositoryInterface
{
    public function findAll(?int $status = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('r')
            ->from(Relay::class, 'r')
            ->orderBy('r.createdAt', 'DESC');

        if ($status !== null) {
            if (EntityStatusEnum::tryFrom($status) === null) {
                throw UnresolvableArgumentException::argumentIsNotSet('Relay device status');
            }

            $qb
                ->where(
                    $qb->expr()->eq('r.status', ':status')
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
        return $this->entityManager->createQueryBuilder()
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
        return $this->entityManager->createQueryBuilder()
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
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Relay::class, 'r')
            ->where('r.topic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}