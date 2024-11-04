<?php

namespace App\Tests\_support\Step\FunctionalStep\Domain\SecureSystem;

use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Domain\SecureSystem\Service\TwoFactorCrudService;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use App\Tests\FunctionalTester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TwoFactorCrudServiceStep extends FunctionalTester
{
    public function getService(?User $user, bool $isEnable = true): TwoFactorCrudService
    {
        return new TwoFactorCrudService(
            repository: $this->grabService(UserRepositoryInterface::class),
            twoFactorService: new TwoFactorService($isEnable),
            transaction: $this->grabService(TransactionInterface::class)
        );
    }

    public function getRequestWithSession(): Request
    {
        $request = new Request();
        $session = $this->grabService(SessionInterface::class);
        $request->setSession($session);

        return $request;
    }
}