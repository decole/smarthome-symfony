<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\ScheduleTask;

use App\Domain\Contract\Repository\ScheduleTaskRepositoryInterface;
use App\Domain\ScheduleTask\Entity\ScheduleTask;
use App\Infrastructure\Repository\BaseDoctrineRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<ScheduleTask>
 */
final class ScheduleTaskRepository extends BaseDoctrineRepository implements ScheduleTaskRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduleTask::class);
    }

    public function findAllActive(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('schedule')
            ->from(ScheduleTask::class, 'schedule')
            ->andWhere(
                $qb->expr()->isNotNull('schedule.nextRun'),
            );

        return $qb->getQuery()->getResult();
    }

    public function findAll(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('schedule')
            ->from(ScheduleTask::class, 'schedule');

        return $qb->getQuery()->getResult();
    }
}
