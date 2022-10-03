<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Doctrine\Relay\Entity\Relay;

interface RelayRepositoryInterface
{
    public function findAll(?int $status = null): array;

    public function findById(string $id): ?Relay;

    public function findByName(string $value): ?Relay;

    public function findByTopic(string $value): ?Relay;
}