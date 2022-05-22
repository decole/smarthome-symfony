<?php


namespace App\Domain\Doctrine\Identity\Entity;


use App\Domain\Doctrine\Common\Traits\CreatedAt;
use App\Domain\Doctrine\Common\Traits\Entity;
use App\Domain\Doctrine\Common\Traits\SoftDelete;
use App\Domain\Doctrine\Common\Traits\UpdatedAt;
use App\Infrastructure\Security\Auth\SecurityHashHelper;
use Webmozart\Assert\Assert;

class Token
{
    use Entity, CreatedAt, UpdatedAt, SoftDelete;

    public const CONFIRM_TYPE = 'confirm';
    public const RESET_TYPE = 'reset';
    public const API_TYPE = 'api';
    public const INVITE_TYPE = 'invite';

    private string $token;

    protected function __construct(private string $tokenType, private User $user)
    {
        Assert::inArray($tokenType, [
            self::CONFIRM_TYPE,
            self::RESET_TYPE,
            self::API_TYPE,
            self::INVITE_TYPE,
        ]);

        $this->token = SecurityHashHelper::generateRandomStringWithoutSpecialChars();

        $this->identify();
        $this->onCreated();
    }

    public static function api(User $user): self
    {
        return new Token(self::API_TYPE, $user);
    }

    public static function confirm(User $user): self
    {
        return new Token(self::CONFIRM_TYPE, $user);
    }

    public static function reset(User $user): self
    {
        return new Token(self::RESET_TYPE, $user);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isReset(string $tokenType): bool
    {
        return $tokenType === self::RESET_TYPE;
    }
}