<?php

declare(strict_types=1);

namespace App\Domain\SecureSystem\Service;

use App\Application\Http\Web\Auth\Dto\ForgotMyPasswordDto;
use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Identity\Entity\User;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use League\FactoryMuffin\Faker\Faker;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Application\Helper\StringHelper;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RestorePasswordService
{
    private const RESTORE_ROUTE = 'app_restore_password';

    private Faker $faker;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepositoryInterface $repository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly MailerInterface $mailer,
        private readonly TransactionInterface $transaction,
        private readonly TranslatorInterface $translator,
        private readonly string $email,
        private readonly string $subject
    ) {
        $this->faker = new Faker();
    }

    /**
     * @throws Exception|TransportExceptionInterface
     */
    public function sendRestoreTokenByEmail(ForgotMyPasswordDto $dto): void
    {
        $user = $this->repository->findOneByEmail(StringHelper::sanitize($dto->email));

        if ($user === null) {
            return;
        }

        $this->transaction->transactional(function () use ($user) {
            $user->setUnverified();
            $user->generateRestoreToken();
            $this->repository->add($user);
        });

        $email = (new TemplatedEmail())
            ->from(new Address($this->email,  $this->subject))
            ->to($user->getEmail())
            ->subject($this->subject)
            ->htmlTemplate('email/restore_email.html.twig')
            ->context([
                'url' => $this->getUrl($user->getRestoreToken()),
                'date' => $user->getExpiredRestoreTokenDate()->format('d.m.Y H:i:s'),
            ]);

        $this->mailer->send($email);
    }

    public function restoreByToken(string $token): array
    {
        $error = null;
        $status = false;

        $user = $this->repository->findByRestoreToken($token);

        if ($user === null) {
            $error = $this->translator->trans('Wrong restore token');
        }

        if ($user->isRestoreTokenExpired()) {
            $error = $this->translator->trans('Token is expired');
        }

        if (!$user->isRestoreTokenExpired()) {
            $status = true;
            $this->restorePassword($user);
        }

        return [$error, $status];
    }

    private function getUrl(string $token): string
    {
        return $this->urlGenerator->generate(
            self::RESTORE_ROUTE,
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function restorePassword(User $user): void
    {
        $password = $this->faker->getGenerator()->password(12);

        $this->transaction->transactional(function () use ($user, $password) {
            $user->setVerified();
            $user->cleanRestoreToken();
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $this->repository->add($user);
        });

        $email = (new TemplatedEmail())
            ->from(new Address($this->email,  $this->subject))
            ->to($user->getEmail())
            ->subject(sprintf('%s %s', $this->translator->trans('Restore password by'), $this->subject))
            ->htmlTemplate('email/new_restored_password_by_email.html.twig')
            ->context([
                'password' => $password,
            ]);

        $this->mailer->send($email);
    }
}