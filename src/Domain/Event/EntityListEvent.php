<?php

namespace App\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class EntityListEvent extends Event
{
    public const NAME = 'page.collector.entity.map';

    private array $map = [];

    public function getEntityMap(): array
    {
        return $this->map;
    }

    public function setEntityMapByType(string $type, array $entities): void
    {
        $this->map[$type] = $entities;
    }
}