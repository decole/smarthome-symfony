<?php

declare(strict_types=1);

namespace App\Infrastructure\TwoFactor\Service;

use App\Domain\Identity\Entity\User;
use App\Infrastructure\TwoFactor\Dto\TwoFactorResultDto;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\HttpFoundation\Request;

final class TwoFactorService
{
    public const NAME = '2fa';
    public const TEMPORARY_KEY = 'temp_secret';

    public function __construct(private readonly bool $isEnable)
    {
    }

    public function isEnabled(): bool
    {
        return $this->isEnable;
    }

    // checking the code from the user and saving the special flag to the session
    public function checkCode(User $user, ?string $code, Request $request): TwoFactorResultDto
    {
        if (empty($code)) {
            return new TwoFactorResultDto(false, 'Empty code');
        }

        if(!$this->validateCode($user->getTwoFactorCode(), $code)) {
            return new TwoFactorResultDto(false, 'Not correct code');
        }

        $this->setSessionIsVerifiedState($user, $request);

        return new TwoFactorResultDto(true);
    }

    public function setSessionIsVerifiedState(User $user, Request $request): void
    {
        $request->getSession()->set(self::NAME, md5($user->getTwoFactorCode()));
    }

    public function deleteSessionVerifiedState(Request $request): void
    {
        $request->getSession()->remove(self::NAME);
    }

    // check flag from session
    public function isConfirm(User $user, Request $request): bool
    {
        $key = $request->getSession()->get(self::NAME);

        return !(empty($key) || $key !== md5($user->getTwoFactorCode()));
    }

    public function validateCode(string $secret, ?string $code): bool|int
    {
        if (empty($code)) {
            return false;
        }

        return (new Google2FA())->verifyKey($secret, $code);
    }

    public function getTemporarySecret(Request $request): string
    {
        $secret = $request->getSession()->get(self::TEMPORARY_KEY);

        if (empty($secret)) {
            $newSecret = (new Google2FA())->generateSecretKey();

            $request->getSession()->set(self::TEMPORARY_KEY, $newSecret);

            $secret = $newSecret;
        }

        return $secret;
    }
}