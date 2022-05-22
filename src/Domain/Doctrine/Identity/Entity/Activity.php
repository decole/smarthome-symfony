<?php


namespace App\Domain\Doctrine\Identity\Entity;


use App\Domain\Doctrine\Common\Traits\LoggedAt;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;
use Webmozart\Assert\Assert;

class Activity
{
    use UpdatedAt, LoggedAt;

    private $isBlocked;
    private $isConfirmed;
    private $registeredAt;

    public function __construct()
    {
        $this->isConfirmed = false;
        $this->isBlocked = false;

        $this->onRegistered();
    }

    public function onRegistered()
    {
        $this->registeredAt = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
    }

    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function block(): void
    {
        Assert::false(
            $this->isBlocked(),
            'user must not be blocked'
        );

        $this->isBlocked = true;
    }

    public function unblock(): void
    {
        Assert::true(
            $this->isBlocked(),
            'user must be blocked'
        );

        $this->isBlocked = false;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function confirm(): void
    {
        Assert::false(
            $this->isConfirmed(),
            'user must be unconfirmed'
        );

        $this->isConfirmed = true;
        $this->onUpdated();
    }
}