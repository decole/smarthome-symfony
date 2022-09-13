<?php

namespace App\Domain\Doctrine\Common\Traits;

use DateTimeImmutable;

trait UpdatedAt
{
    protected ?DateTimeImmutable $updatedAt;

    final public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    final public function onUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable('now', new \DateTimeZone('utc'));
    }
}