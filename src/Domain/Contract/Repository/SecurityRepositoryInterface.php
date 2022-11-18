<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Security\Entity\Security;

interface SecurityRepositoryInterface
{
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?Security;

    public function findByName(string $value): ?Security;

    public function findByTopic(string $value): ?Security;
}