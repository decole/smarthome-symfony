<?php

namespace App\Domain\Identity\Entity;

use App\Domain\Common\Traits\Entity;
use App\Domain\Contract\Repository\EntityInterface;
use DateTimeImmutable;
use Exception;
use League\FactoryMuffin\Faker\Faker;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface
{
    use Entity;

    private const EXPIRED_SECONDS = 3600;

    public const ROLE_USER = 'ROLE_USER';

    private string $name;

    private $email;

    private $roles = [];

    private $password;

    private bool $isVerified;

    private ?int $telegramId = null;

    private ?string $restoreToken = null;

    private ?DateTimeImmutable $restoreTokenCreatedAt = null;

    public function __construct()
    {
        $this->identify();
        $this->isVerified = false;
    }

    public function getLogin(): string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(): void
    {
        $this->isVerified = true;
    }

    public function setUnverified(): void
    {
        $this->isVerified = false;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getTelegramId(): ?int
    {
        return $this->telegramId;
    }

    public function setTelegramId(?int $telegramId): void
    {
        $this->telegramId = $telegramId;
    }

    public function getImageGravatar(): string
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->email) . '.jpg';
    }

    public static function alias(): string
    {
        return 'user';
    }

    public function getRestoreToken(): ?string
    {
        return $this->restoreToken;
    }

    /**
     * @throws Exception
     */
    public function generateRestoreToken(): void
    {
        $this->restoreToken = md5(time() . (new Faker())->getGenerator()->text(1000));
        $this->restoreTokenCreatedAt = new DateTimeImmutable('now');
    }

    public function isRestoreTokenExpired(): bool
    {
        return $this->restoreTokenCreatedAt === null ||
            time() > $this->getExpiredRestoreTokenDate()->getTimestamp();
    }

    public function getRestoreTokenDate(): DateTimeImmutable
    {
        return $this->restoreTokenCreatedAt;
    }

    public function getExpiredRestoreTokenDate(): DateTimeImmutable
    {
        $seconds = self::EXPIRED_SECONDS;

        return $this->getRestoreTokenDate()->modify("+{$seconds} second");
    }

    public function cleanRestoreToken(): void
    {
        $this->restoreToken = null;
        $this->restoreTokenCreatedAt = null;
    }
}