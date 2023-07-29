<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\Page\Entity\Page;

/**
 * Contract for a Doctrine persistence layer ObjectRepository class to implement.
 *
 * @template-covariant T of object
 */
interface PageRepositoryInterface
{
    /**
     * Finds all objects in the repository.
     *
     * @return array<int, object> The objects.
     * @psalm-return T[]
     */
    public function findAll(): array;

    public function findByName(string $page): ?Page;

    public function findById(string $id): ?Page;
}