<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait Entity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
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
