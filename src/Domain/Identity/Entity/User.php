<?php

declare(strict_types=1);

namespace App\Domain\Identity\Entity;

use App\Domain\Common\Traits\Entity;
use App\Domain\Contract\Repository\EntityInterface;
use App\Infrastructure\Repository\Identity\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface
{
    use Entity;

    private const EXPIRED_SECONDS = 3600;
    final public const ROLE_USER = 'ROLE_USER';

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 180, nullable: true)]
    #[Assert\Email]
    private ?string $email;

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: Types::STRING)]
    private string $password;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $telegramId = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $restoreToken = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $restoreTokenCreatedAt = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $googleAuthSecret = null;

    public function __construct()
    {
        $this->identify();
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
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
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
    public function eraseCredentials(): void
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
     * @throws \Exception
     */
    public function generateRestoreToken(): void
    {
        $this->restoreToken = md5(time() . random_int(0, 100000));
        $this->restoreTokenCreatedAt = new \DateTimeImmutable('now');
    }

    public function isRestoreTokenExpired(): bool
    {
        return !$this->restoreTokenCreatedAt instanceof \DateTimeImmutable
            || time() > $this->getExpiredRestoreTokenDate()->getTimestamp();
    }

    public function getRestoreTokenDate(): \DateTimeImmutable
    {
        return $this->restoreTokenCreatedAt;
    }

    public function getExpiredRestoreTokenDate(): \DateTimeImmutable
    {
        $seconds = self::EXPIRED_SECONDS;

        return $this->getRestoreTokenDate()->modify("+{$seconds} second");
    }

    public function cleanRestoreToken(): void
    {
        $this->restoreToken = null;
        $this->restoreTokenCreatedAt = null;
    }

    public function getTwoFactorSecret(): ?string
    {
        return $this->googleAuthSecret;
    }

    public function setTwoFactorSecret(?string $googleAuthSecret): void
    {
        $this->googleAuthSecret = $googleAuthSecret;
    }

    public function isTwoFactorEnable(): bool
    {
        return (bool) $this->googleAuthSecret;
    }

    /**
     * @return array{int|null, string|null, string|null}
     */
    public function __serialize(): array
    {
        return [$this->id, $this->email, $this->password];
    }

    /**
     * @param array{int|null, string, string} $data
     */
    public function __unserialize(array $data): void
    {
        [$this->id, $this->email, $this->password] = $data;
    }
}
