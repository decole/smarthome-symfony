<?php

namespace App\Infrastructure\Security\Register\Service;

use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Identity\Entity\User;
use App\Domain\SecureSystem\Dto\RegisterDto;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
use App\Infrastructure\Security\Auth\Service\EmailVerifyService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService
{
    public function __construct(
        private TransactionInterface $transaction,
        private UserRepository $userRepository,
        private EmailVerifyService $emailVerifier,
        private UserPasswordHasherInterface $passwordHasher,
        private string $email,
        private string $subject
    ) {
    }

    final public function signUp(RegisterDto $dto): User
    {
        $user = new User();
        $user->setName($dto->getName());
        $user->setEmail($dto->getEmail());
        $user->setRoles([User::ROLE_USER]);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));

        $this->transaction->transactional(
            function () use ($user) {
                $this->userRepository->add($user, true);
            }
        );

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->email,  $this->subject))
                ->to($user->getEmail())
        ->subject('Please Confirm your Email')
        ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $user;
    }
}