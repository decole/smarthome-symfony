<?php

namespace App\Infrastructure\Doctrine\Repository\VisualNotification;

use App\Domain\Contract\Repository\VisualNotificationRepositoryInterface;
use App\Domain\Doctrine\VisualNotification\Entity\VisualNotification;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use DateTimeImmutable;
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
            ->setParameter('updatedAt', new DateTimeImmutable('now', new \DateTimeZone('utc')));

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
}