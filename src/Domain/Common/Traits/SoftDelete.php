<?php

namespace App\Domain\Common\Traits;

use DateTimeImmutable;
use DateTimeZone;

trait SoftDelete
{
    private ?DateTimeImmutable $deletedAt = null;
    protected bool $isDeleted = false;

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function delete(): void
    {
        $this->deletedAt = new DateTimeImmutable('now', new DateTimeZone('utc'));
        $this->isDeleted = true;
    }
}