<?php

namespace App\Domain\Contract\Repository;

use App\Domain\PLC\Entity\PLC;

interface PlcRepositoryInterface
{
    /**
     * @param int|null $status
     * @return list<PLC>
     */
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?PLC;

    public function findByName(string $value): ?PLC;

    public function findByTargetTopic(string $value): ?PLC;
}