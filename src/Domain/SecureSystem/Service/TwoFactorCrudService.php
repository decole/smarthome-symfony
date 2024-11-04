<?php

declare(strict_types=1);

namespace App\Domain\SecureSystem\Service;

use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Domain\Common\Transactions\TransactionInterface;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Domain\Identity\Entity\User;

final class TwoFactorCrudService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly TwoFactorService $twoFactorService,
        private readonly TransactionInterface $transaction
    ) {
    }

    public function add(UserInterface $user, string $secret): void
    {
        if (!$user instanceof User) {
            $user = $this->repository->findOneByEmail($user->getUserIdentifier());
        }

        $this->transaction->transactional(fn() => $user->setAuthSecret($secret));
    }

    public function delete(UserInterface $user, Request $request): void
    {
        if (!$user instanceof User) {
            $user = $this->repository->findOneByEmail($user->getUserIdentifier());
        }

        $this->transaction->transactional(fn() => $user->setAuthSecret(null));

        $this->twoFactorService->deleteSessionVerifiedState($request);
    }
}