<?php

declare(strict_types=1);

namespace App\Domain\Page\Dto;

use App\Domain\Contract\Repository\EntityInterface;

class PageEntityDto
{
    public string $type;

    public EntityInterface $entity;
}