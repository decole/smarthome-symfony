<?php

namespace App\Infrastructure\Doctrine\Repository\ScheduleTask;

use App\Domain\Contract\Repository\ScheduleTaskRepositoryInterface;
use App\Domain\ScheduleTask\Entity\ScheduleTask;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;

final class ScheduleTaskRepository extends BaseDoctrineRepository implements ScheduleTaskRepositoryInterface
{
    public function findAllActive(): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('schedule')
            ->from(ScheduleTask::class, 'schedule')
            ->andWhere(
                $qb->expr()->isNotNull('schedule.nextRun')
            );

        return $qb->getQuery()->getResult();
    }

    public function findAll(): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('schedule')
            ->from(ScheduleTask::class, 'schedule');

        return $qb->getQuery()->getResult();
    }
}