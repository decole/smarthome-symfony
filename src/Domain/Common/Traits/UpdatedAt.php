<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

use Doctrine\ORM\Mapping as ORM;

trait UpdatedAt
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $updatedAt;

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function onUpdated(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
    }
}
