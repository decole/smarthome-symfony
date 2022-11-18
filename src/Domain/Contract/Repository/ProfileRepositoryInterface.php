<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Doctrine\Identity\Entity\User;

interface ProfileRepositoryInterface
{
    public function findById(string $id): ?User;

    public function isExistDuplicateEmail(string $login, string $email): bool;
}