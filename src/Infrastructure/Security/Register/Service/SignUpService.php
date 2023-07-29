<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Register\Service;

use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Identity\Entity\User;
use App\Domain\SecureSystem\Dto\RegisterDto;
use App\Infrastructure\Repository\Identity\UserRepository;
use App\Infrastructure\Security\Auth\Service\EmailVerifyService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SignUpService
{
    public function __construct(
        private readonly TransactionInterface $transaction,
        private readonly UserRepository $userRepository,
        private readonly EmailVerifyService $emailVerifier,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly string $email,
        private readonly string $subject
    ) {
    }

    public function signUp(RegisterDto $dto): User
    {
        $user = new User();
        $user->setName($dto->getName());
        $user->setEmail($dto->getEmail());
        $user->setRoles([User::ROLE_USER]);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));

        $this->transaction->transactional(
            fn () => $this->userRepository->add($user, true)
        );

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->email,  $this->subject))
                ->to($user->getEmail())
        ->subject('Please Confirm your Email')
        ->htmlTemplate('email/confirmation_email.html.twig')
        );

        return $user;
    }
}