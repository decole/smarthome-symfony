<?php

namespace App\Infrastructure\Repository\PLC;

use App\Domain\Common\Enum\EntityStatusEnum;
use App\Domain\Common\Exception\UnresolvableArgumentException;
use App\Domain\Contract\Repository\PlcRepositoryInterface;
use App\Domain\PLC\Entity\PLC;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;

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
            if (EntityStatusEnum::tryFrom($status) === null) {
                throw UnresolvableArgumentException::argumentIsNotSet('PLC status');
            }

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