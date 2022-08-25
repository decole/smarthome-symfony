<?php

namespace App\Domain\Doctrine\Common\Traits;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait Entity
{
    protected UuidInterface $id;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    protected function identify(): void
    {
        $this->id = Uuid::uuid4();
    }
}
