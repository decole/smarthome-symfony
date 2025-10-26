<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAt
{
    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $createdAt;

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function onCreated(): void
    {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
    }
}
