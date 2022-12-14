<?php

namespace App\Domain\Common\Traits;


trait LoggedAt
{
    protected $lastLoginAt;

    public function onLogged()
    {
        $this->lastLoginAt = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }
}