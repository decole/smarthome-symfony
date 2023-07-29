<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\PLC\Entity\PLC;

/**
 * Contract for a Doctrine persistence layer ObjectRepository class to implement.
 *
 * @template-covariant T of object
 */
interface PlcRepositoryInterface
{
    /**
     * Finds all objects in the repository.
     *
     * @return array<int, object> The objects.
     * @psalm-return T[]
     */
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?PLC;

    public function findByName(string $value): ?PLC;

    public function findByTargetTopic(string $value): ?PLC;
}