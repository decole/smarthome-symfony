<?php

namespace App\Infrastructure\Security\Auth\Service;

use App\Domain\Identity\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class EmailVerifyService
{
    public function __construct(
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function sendEmailConfirmation(
        string $verifyEmailRouteName,
        UserInterface $user,
        TemplatedEmail $email
    ): void {
        /** @var User $user */
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string)$user->getId(),
            $user->getEmail(),
            ['id' => (string)$user->getId()]
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        /** @var User $user */
        $this->verifyEmailHelper->validateEmailConfirmation(
            $request->getUri(),
            (string)$user->getId(),
            $user->getEmail()
        );

        $user->setVerified();

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}