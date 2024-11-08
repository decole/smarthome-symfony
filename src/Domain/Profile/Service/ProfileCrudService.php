<?php

declare(strict_types=1);

namespace App\Domain\Profile\Service;

use App\Application\Http\Web\Profile\Dto\CrudProfileDto;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Identity\Entity\User;
use App\Domain\Profile\Factory\ProfileCrudFactory;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ProfileCrudService
{
    private const EMAIL_ALIAS = 'email';
    private const IS_CHANGE_PASSWORD_ALIAS = 'is_change';
    private const TELEGRAM_ALIAS = 'telegram_id';
    private const PASSWORD_ALIAS = 'password';
    private const PASSWORD_AGAN_ALIAS = 'password_agan';

    public function __construct(
        private readonly ProfileCrudFactory $crud,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function validate(CrudProfileDto $dto): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($dto);

        return $this->crud->validate(true);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function update(User $user, CrudProfileDto $dto): EntityInterface
    {
        $user->setName($dto->login);
        $user->setEmail($dto->email);
        $user->setTelegramId($dto->telegramId);

        if ($dto->isChangePassword !== null) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));

            $event = new AlertNotificationEvent(
                message: "{$dto->login} profile password changed. New password: {$dto->password}",
                types: [AlertNotificationEvent::MESSENGER]
            );
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }

        return $this->crud->save($user);
    }

    public function createDto(string $login, Request $request): CrudProfileDto
    {
        $dto = new CrudProfileDto();

        $dto->login = $login;
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $dto->email = (string)$request->request->get(self::EMAIL_ALIAS);
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $dto->telegramId = $this->integerOrNull($request->request->get(self::TELEGRAM_ALIAS));
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $dto->isChangePassword = $request->request->get(self::IS_CHANGE_PASSWORD_ALIAS);
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $dto->password = $request->request->get(self::PASSWORD_ALIAS);
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $dto->passwordAgan = $request->request->get(self::PASSWORD_AGAN_ALIAS);

        return $dto;
    }

    private function integerOrNull(mixed $value): ?int
    {
        return $value === null ? $value : (int)$value;
    }
}