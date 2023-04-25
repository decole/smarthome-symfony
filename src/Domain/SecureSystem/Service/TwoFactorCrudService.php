<?php

namespace App\Domain\SecureSystem\Service;

use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Domain\Common\Transactions\TransactionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Domain\Identity\Entity\User;

final class TwoFactorCrudService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
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

    public function delete(UserInterface $user): void
    {
        if (!$user instanceof User) {
            $user = $this->repository->findOneByEmail($user->getUserIdentifier());
        }

        $this->transaction->transactional(fn() => $user->setAuthSecret(null));
    }
}