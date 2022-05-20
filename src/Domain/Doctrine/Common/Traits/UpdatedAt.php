<?php

namespace App\Domain\Doctrine\Common\Traits;


use DateTimeImmutable;

trait UpdatedAt
{
    protected $updatedAt;

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    protected function onUpdated()
    {
        $this->updatedAt = new DateTimeImmutable('now', new \DateTimeZone('utc'));
    }
}
