<?php


namespace App\Domain\Doctrine\Identity\Entity;


use App\Infrastructure\Security\Auth\SecurityHashHelper;

class Auth
{
    private string $passwordHash;
    private string $authKey;

    public function __construct(private string $login, string $password)
    {
        $this->passwordHash = SecurityHashHelper::generatePasswordHash($password);
        $this->authKey = SecurityHashHelper::generateRandomString(64);
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    public static function changePassword(self $another, string $password): Auth
    {
        return new self($another->login, $password);
    }
}