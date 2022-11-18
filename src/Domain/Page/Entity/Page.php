<?php

namespace App\Domain\Page\Entity;

use App\Domain\Common\Traits\CreatedAt;
use App\Domain\Common\Traits\Entity;
use App\Domain\Common\Traits\UpdatedAt;
use App\Domain\Contract\Repository\EntityInterface;

final class Page implements EntityInterface
{
    use Entity, CreatedAt, UpdatedAt;

    public function __construct(
        private string $name,
        private array $config
    ) {
        $this->identify();
        $this->onCreated();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public static function alias(): string
    {
        return 'page';
    }
}