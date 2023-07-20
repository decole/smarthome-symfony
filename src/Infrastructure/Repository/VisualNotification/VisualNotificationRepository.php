<?php

namespace App\Infrastructure\Repository\VisualNotification;

use App\Domain\Contract\Repository\VisualNotificationRepositoryInterface;
use App\Domain\VisualNotification\Entity\VisualNotification;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Webmozart\Assert\Assert;

final class VisualNotificationRepository extends BaseDoctrineRepository implements VisualNotificationRepositoryInterface
{
    public function setAllIsRead(?int $type = null): void
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->update(VisualNotification::class, 'v')
            ->set('v.isRead', ':updateIsRead')
            ->set('v.updatedAt', ':updatedAt')
            ->where(
                $qb->expr()->eq('v.isRead', ':isRead')
            )
            ->setParameter('isRead', false)
            ->setParameter('updateIsRead', true)
            ->setParameter('updatedAt', new DateTimeImmutable('now', new DateTimeZone('UTC')), Types::DATE_IMMUTABLE);

        if ($type !== null) {
            Assert::inArray($type, VisualNotification::TYPE);

            $qb
                ->andWhere(
                    $qb->expr()->eq('v.type', ':type')
                )
                ->setParameter('type', $type);
        }

        $qb->getQuery()->execute();
    }

    public function findByTypeAndIsRead(
        ?int $type = null,
        ?bool $isRead = null
    ): array {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('v')
            ->from(VisualNotification::class, 'v')
            ->orderBy('v.createdAt', 'DESC');

        if ($type !== null) {
            Assert::inArray($type, VisualNotification::TYPE);

            $qb
                ->andWhere(
                    $qb->expr()->eq('v.type', ':type')
                )
                ->setParameter('type', $type);
        }

        if ($isRead !== null) {
            $qb
                ->andWhere(
                    $qb->expr()->eq('v.isRead', ':isRead')
                )
                ->setParameter('isRead', $isRead);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByFilters(Criteria $criteria): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('v')
            ->from(VisualNotification::class, 'v');

        $qb->addCriteria($criteria);

        return $qb->getQuery()->getResult();
    }

    public function count(): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('count(v.id)')
            ->from(VisualNotification::class, 'v');

        return $qb->getQuery()->getSingleScalarResult();
    }
}