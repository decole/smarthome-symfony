<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Doctrine\Page\Entity\Page;

interface PageRepositoryInterface
{
    public function save(EntityInterface $sensor): EntityInterface;

    public function findAll(): array;

    public function findByName(string $page): ?Page;

    public function findById(string $id): ?Page;
}