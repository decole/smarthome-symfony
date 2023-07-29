<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\FireSecurity\Entity\FireSecurity;

/**
 * Contract for a Doctrine persistence layer ObjectRepository class to implement.
 *
 * @template-covariant T of object
 */
interface FireSecurityRepositoryInterface
{
    /**
     * Finds all objects in the repository.
     *
     * @return array<int, object> The objects.
     * @psalm-return T[]
     */
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?FireSecurity;

    public function findByName(string $value): ?FireSecurity;

    public function findByTopic(string $value): ?FireSecurity;
}