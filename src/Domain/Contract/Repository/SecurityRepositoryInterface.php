<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Security\Entity\Security;

/**
 * Contract for a Doctrine persistence layer ObjectRepository class to implement.
 *
 * @template-covariant T of object
 */
interface SecurityRepositoryInterface
{
    /**
     * Finds all objects in the repository.
     *
     * @return array<int, object> The objects.
     * @psalm-return T[]
     */
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?Security;

    public function findByName(string $value): ?Security;

    public function findByTopic(string $value): ?Security;
}