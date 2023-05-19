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
     * Contract for a Doctrine persistence layer ObjectRepository class to implement.
     *
     * @template-covariant T of object
     */
    public function findAll(): array;
}