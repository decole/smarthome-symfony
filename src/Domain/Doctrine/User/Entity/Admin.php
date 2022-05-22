<?php

namespace App\Domain\Doctrine\User\Entity;

use App\Domain\Doctrine\Identity\Entity\Auth;
use App\Domain\Doctrine\Identity\Entity\Contact;
use App\Domain\Doctrine\Identity\Entity\User;

class Admin extends User
{
    private ?string $name;

    public static function signUp(Auth $auth, string $email, ?string $telegram, ?int $telegramId): User
    {
        return new self($auth, new Contact($email, $telegram, $telegramId));
    }

    final public function getName(): ?string
    {
        return $this->name;
    }

    final public function setName(string $name): Admin
    {
        $this->name = $name;

        return $this;
    }
}