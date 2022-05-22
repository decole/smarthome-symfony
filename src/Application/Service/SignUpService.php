<?php


namespace App\Application\Service;


use App\Domain\Doctrine\Common\Transactions\TransactionInterface;
use App\Domain\Doctrine\Identity\Entity\Auth;
use App\Domain\Doctrine\Identity\Entity\Contact;
use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\SecureSystem\Dto\RegisterDto;
use App\Domain\Doctrine\User\Entity\Admin;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
use App\Infrastructure\Security\Auth\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class SignUpService
{
    public function __construct(
        private TransactionInterface $transaction,
        private UserRepository $userRepository,
        private EmailVerifier $emailVerifier,
        private string $email,
        private string $subject,
    ) {

    }

    final public function sugnUp(RegisterDto $dto): User
    {
        /** @var Admin $user */
        $user = $this->transaction->transactional(
            function () use ($dto) {
                $user = new Admin(
                    new Auth($dto->getName(), $dto->getPassword()),
                    new Contact($dto->getEmail())
                );

                $this->userRepository->save($user);

                return $user;
            }
        );

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->email,  $this->subject))
                ->to($user->getContact()->getEmail())
        ->subject('Please Confirm your Email')
        ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $user;
    }
}