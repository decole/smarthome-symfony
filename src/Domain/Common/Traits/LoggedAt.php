<?php

declare(strict_types=1);

namespace App\Domain\Common\Traits;

use Doctrine\ORM\Mapping as ORM;

trait LoggedAt
{
    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $lastLoginAt;

    public function onLogged(): void
    {
        $this->lastLoginAt = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }
}
