<?php


namespace App\Domain\Doctrine\Identity\Entity;


use App\Domain\Doctrine\Common\Traits\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[UniqueEntity(fields: ['auth.login'], message: 'There is already an account with this auth.login')]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Entity;

    protected Activity $activity;
    protected $email;
    protected $roles = [];
    protected $password;
    protected bool $isMessagesEnabled = false;
    private $isVerified = false;

    public function __construct(private Auth $auth, private Contact $contact)
    {
        $this->identify();
        $this->activity = new Activity();
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }

    public function changeAuth(Auth $auth): void
    {
        $this->auth = $auth;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function changeContact(Contact $contact): void
    {
        $this->contact = $contact;
    }

    public function getType(): string
    {
        $fullClassName = explode(
            "\\",
            strtolower(static::class)
        );
        return end($fullClassName);
    }

    public function getUserIdentifier(): string
    {
        return $this->auth->getLogin();
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->auth->getLogin();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    abstract public static function signUp(Auth $auth, string $email, ?string $telegram, ?int $telegramId): User;

    public function isVerified(): bool
    {
        return $this->activity->isConfirmed();
    }

    public function setIsVerified(): self
    {
        $this->activity->confirm();

        return $this;
    }
}
