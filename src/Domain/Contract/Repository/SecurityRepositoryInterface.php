<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Doctrine\Security\Entity\Security;

interface SecurityRepositoryInterface
{
    public function save(EntityInterface $sensor): EntityInterface;

    public function findAll(?int $status = null): array;

    public function findById(string $id): ?Security;

    public function findByName(string $value): ?Security;

    public function findByTopic(string $value): ?Security;
}
