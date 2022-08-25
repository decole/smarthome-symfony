<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;

interface FireSecurityRepositoryInterface
{
    public function save(EntityInterface $sensor): EntityInterface;

    public function findAll(?int $status = null): array;

    public function findById(string $id): ?FireSecurity;

    public function findByName(string $value): ?FireSecurity;

    public function findByTopic(string $value): ?FireSecurity;
}
