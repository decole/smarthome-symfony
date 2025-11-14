<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\VisualNotification;

use App\Domain\Contract\Repository\VisualNotificationRepositoryInterface;
use App\Domain\VisualNotification\Entity\VisualNotification;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @template-extends ServiceEntityRepository<VisualNotification>
 */
final class VisualNotificationRepository extends BaseDoctrineRepository implements VisualNotificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisualNotification::class);
    }

    public function setAllIsRead(?int $type = null): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->update(VisualNotification::class, 'v')
            ->set('v.isRead', ':updateIsRead')
            ->set('v.updatedAt', ':updatedAt')
            ->where(
                $qb->expr()->eq('v.isRead', ':isRead'),
            )
            ->setParameter('isRead', false)
            ->setParameter('updateIsRead', true)
            ->setParameter('updatedAt', new \DateTimeImmutable('now', new \DateTimeZone('UTC')), Types::DATE_IMMUTABLE);

        if (null !== $type) {
            Assert::inArray($type, VisualNotification::TYPE);

            $qb
                ->andWhere(
                    $qb->expr()->eq('v.type', ':type'),
                )
                ->setParameter('type', $type);
        }

        $qb->getQuery()->execute();
    }

    public function findByTypeAndIsRead(
        ?int $type = null,
        ?bool $isRead = null,
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('v')
            ->from(VisualNotification::class, 'v')
            ->orderBy('v.createdAt', 'DESC');

        if (null !== $type) {
            Assert::inArray($type, VisualNotification::TYPE);

            $qb
                ->andWhere(
                    $qb->expr()->eq('v.type', ':type'),
                )
                ->setParameter('type', $type);
        }

        if (null !== $isRead) {
            $qb
                ->andWhere(
                    $qb->expr()->eq('v.isRead', ':isRead'),
                )
                ->setParameter('isRead', $isRead);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByFilters(Criteria $criteria): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('v')
            ->from(VisualNotification::class, 'v');

        $qb->addCriteria($criteria);

        return $qb->getQuery()->getResult();
    }
}
