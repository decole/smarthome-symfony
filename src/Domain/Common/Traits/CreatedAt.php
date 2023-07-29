<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

use DateTimeImmutable;

trait CreatedAt
{
    protected DateTimeImmutable $createdAt;

    final public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    final public function onCreated(): void
    {
        $this->createdAt = new DateTimeImmutable('now', new \DateTimeZone('utc'));
    }
}