<?php

namespace App\Domain\Contract\Repository;

use App\Domain\Page\Entity\Page;

interface PageRepositoryInterface
{
    public function findAll(): array;

    public function findByName(string $page): ?Page;

    public function findById(string $id): ?Page;
}