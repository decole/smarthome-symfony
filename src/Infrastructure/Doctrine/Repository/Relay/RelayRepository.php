<?php


namespace App\Infrastructure\Doctrine\Repository\Relay;


use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Interfaces\RelayRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Doctrine\ORM\NonUniqueResultException;
use Webmozart\Assert\Assert;

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
            Assert::inArray($status, Relay::STATUS_MAP);

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