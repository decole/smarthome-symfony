<?php

namespace App\Domain\Doctrine\Page\Factory;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\Page\Dto\PageEntityDto;
use App\Infrastructure\Doctrine\Factory\OutputFactoryInterface;

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