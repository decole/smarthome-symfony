<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Sensor\Entity\Sensor;

interface SensorRepositoryInterface
{
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?Sensor;

    public function findByName(string $value): ?Sensor;

    public function findByTopic(string $value): ?Sensor;
}