<?php

namespace App\Domain\Contract\Repository;

use App\Domain\PLC\Entity\PLC;

interface PlcRepositoryInterface
{
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?PLC;

    public function findByName(string $value): ?PLC;

    public function findByTargetTopic(string $value): ?PLC;
}