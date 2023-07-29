<?php

declare(strict_types=1);

namespace App\Domain\Page\Factory;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Page\Dto\PageEntityDto;
use App\Infrastructure\Output\Factory\OutputFactoryInterface;

final class PageEntityDtoFactory implements OutputFactoryInterface
{
    public function create(EntityInterface $entity): PageEntityDto
    {
        $dto = new PageEntityDto();
        $dto->type = $entity::alias();
        $dto->entity = $entity;

        return $dto;
    }
}