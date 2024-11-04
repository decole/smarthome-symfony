<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

trait LoggedAt
{
    protected $lastLoginAt;

    public function onLogged(): void
    {
        $this->lastLoginAt = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }
}