<?php

namespace App\Infrastructure\Doctrine\Repository\PLC;

use App\Domain\Contract\Repository\PlcRepositoryInterface;
use App\Domain\PLC\Entity\PLC;
use App\Domain\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;
use Webmozart\Assert\Assert;

final class PlcRepository extends BaseDoctrineRepository implements PlcRepositoryInterface
{
    public function findAll(?int $status = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('d')
            ->from(PLC::class, 'd')
            ->orderBy('d.createdAt', 'DESC');

        if ($status !== null) {
            Assert::inArray($status, PLC::STATUS_MAP);

            $qb
                ->where(
                    $qb->expr()->eq('d.status', ':status')
                )
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?PLC
    {
        return $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(PLC::class, 'd')
            ->where('d.id = :value')
            ->setParameter('value', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByName(string $value): ?PLC
    {
        return $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(PLC::class, 'd')
            ->where('d.name = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByTargetTopic(string $value): ?PLC
    {
        return $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(PLC::class, 'd')
            ->where('d.targetTopic = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}