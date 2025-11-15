<?php

declare(strict_types=1);

namespace App\Domain\SecureSystem\Service;

use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TwoFactorCrudService
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private TwoFactorService $twoFactorService,
        private TransactionInterface $transaction,
    ) {}

    public function add(UserInterface $user, string $secret): void
    {
        if (!$user instanceof User) {
            $user = $this->repository->findOneByEmail($user->getUserIdentifier());
        }

        $this->transaction->transactional(fn () => $user->setTwoFactorSecret($secret));
    }

    public function delete(UserInterface $user, Request $request): void
    {
        if (!$user instanceof User) {
            $user = $this->repository->findOneByEmail($user->getUserIdentifier());
        }

        $this->transaction->transactional(fn () => $user->setTwoFactorSecret(null));

        $this->twoFactorService->deleteSessionVerifiedState($request);
    }
}
