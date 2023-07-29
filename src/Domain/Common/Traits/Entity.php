<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait Entity
{
    protected UuidInterface $id;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIdToString(): string
    {
        return $this->id->toString();
    }

    protected function identify(): void
    {
        $this->id = Uuid::uuid4();
    }
}