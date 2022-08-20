<?php


namespace App\Domain\Doctrine\SecureSystem\Service;


use App\Domain\Doctrine\Identity\Repository\UserRepositoryInterface;
use App\Domain\Doctrine\SecureSystem\Dto\RegisterDto;

class RegistrationValidateService
{
    private bool $valid = true;
    private array $errors = [];

    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    final public function validate(RegisterDto $dto, string $csrfSessionToken): array
    {
        $this->validCsfr($dto, $csrfSessionToken);
        $this->checkNotEmpty($dto);
        $this->validEmail($dto);
        $this->validPassword($dto);
        $this->validLoginExist($dto);

        return [$this->isValid(), $this->getErrors()];
    }

    private function validCsfr(RegisterDto $dto, string $csrfSessionToken): void
    {
        if ($csrfSessionToken !== $dto->getCsrf()) {
            $this->addError('csrf', 'Csrf token is not valid!');
        }
    }

    private function checkNotEmpty(RegisterDto $dto): void
    {
        foreach ($dto->toArray() as $field => $value) {
            // terms not require
            if ($field === 'terms') {
                continue;
            }

            if ($value === null) {
                $this->addError($field, "field {$field} is empty");
            }
        }
    }

    private function validLoginExist(RegisterDto $dto): void
    {
        if (!$this->isValid()) {
            return;
        }

        $user = $this->repository->findOneByName($dto->getName());

        if ($user !== null) {
            $this->addError('login', 'Login exist');
        }
    }

    private function validPassword(RegisterDto $dto): void
    {
        if ($dto->getPassword() !== $dto->getRePassword()) {

            $this->addError('password', 'Password and RePassword');
        }

        if (mb_strlen($dto->getPassword()) < 6) {
            $this->addError('password', 'Password string length then > 6 symbols');
        }
    }

    private function addError(string $field, string $error): void
    {
        $this->isNotValid();
        $this->errors[$field] = $error;
    }

    private function validEmail(RegisterDto $dto): void
    {
        if (!filter_var($dto->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Invalid email format');
        }
    }

    private function isValid(): bool
    {
        return $this->valid;
    }

    private function isNotValid(): void
    {
        $this->valid = false;
    }

    private function getErrors(): array
    {
        return $this->errors;
    }
}