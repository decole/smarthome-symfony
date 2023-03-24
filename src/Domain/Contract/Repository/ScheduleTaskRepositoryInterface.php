<?php

namespace App\Domain\Contract\Repository;

use App\Domain\ScheduleTask\Entity\ScheduleTask;

interface ScheduleTaskRepositoryInterface
{
    /**
     * @return ScheduleTask[]
     */
    public function findAllActive(): array;

    /**
     * @return ScheduleTask[]
     */
    public function findAll(): array;
}