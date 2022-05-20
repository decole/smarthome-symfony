<?php

namespace App\Domain\Doctrine\Common\Traits;


use DateTimeImmutable;

trait CreatedAt
{
    protected DateTimeImmutable $createdAt;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    protected function onCreated()
    {
        $this->createdAt = new DateTimeImmutable('now', new \DateTimeZone('utc'));
    }
}
