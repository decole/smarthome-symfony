<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SoftDelete
{
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $deletedAt = null;

    protected bool $isDeleted = false;

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
        $this->isDeleted = true;
    }
}
