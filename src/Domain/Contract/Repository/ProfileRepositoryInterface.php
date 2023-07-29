<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\Identity\Entity\User;

/**
 * Contract for a Doctrine persistence layer ObjectRepository class to implement.
 *
 * @template-covariant T of object
 */
interface ProfileRepositoryInterface
{
    public function findById(string $id): ?User;

    public function isExistDuplicateEmail(string $login, string $email): bool;
}