<?php

declare(strict_types=1);

namespace App\Domain\Identity\Repository;

use App\Domain\Identity\Entity\User;

interface UserRepositoryInterface
{
    public function add(User $entity, bool $flush = false): void;

    public function remove(User $entity, bool $flush = false): void;

    public function findOneByName(string $name): ?User;

    public function findOneByEmail(string $email): ?User;

    public function findAllWithTelegramId(): array;

    public function findByRestoreToken(mixed $token): ?User;
}