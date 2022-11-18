<?php

namespace App\Application\Service\Validation\Profile;

use App\Application\Http\Web\Profile\Dto\CrudProfileDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\ProfileRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileCrudValidationService implements ValidationInterface
{
    /**
     * @var CrudProfileDto
     */
    private ValidationDtoInterface $dto;

    public function __construct(private ValidatorInterface $validator, private ProfileRepositoryInterface $repository)
    {
    }

    public function validate(bool $isUpdate): ConstraintViolationListInterface
    {
        $list = $this->validator->validate($this->dto);

        assert($list instanceof ConstraintViolationList);

        $list = $this->checkPasswordChangeLogic($list);

        return $this->uniqueValidate($list);
    }

    public function setValue(ValidationDtoInterface $dto): void
    {
        $this->dto = $dto;
    }

    private function uniqueValidate(ConstraintViolationList $list): ConstraintViolationListInterface
    {
        if ($this->repository->isExistDuplicateEmail($this->dto->login, $this->dto->email)) {
            $list->add(new ConstraintViolation(
                message: 'Email is exist, enter other email.',
                messageTemplate: null,
                parameters: [$this->dto->email],
                root: 'email',
                propertyPath: 'email',
                invalidValue: $this->dto->email
            ));
        }

        return $list;
    }

    private function checkPasswordChangeLogic(ConstraintViolationList $list): ConstraintViolationListInterface
    {
        if ($this->dto->isChangePassword === 'on') {
            if ($this->dto->password !== $this->dto->passwordAgan) {
                $list->add(new ConstraintViolation(
                    message: 'Password and password agan is not equal.',
                    messageTemplate: null,
                    parameters: [$this->dto->password, $this->dto->passwordAgan],
                    root: ['password', 'password_agan'],
                    propertyPath: 'password',
                    invalidValue: [$this->dto->password, $this->dto->passwordAgan]
                ));
            }

            if (mb_strlen($this->dto->password) < 6) {
                $list->add(new ConstraintViolation(
                    message: 'Password expected 6 or more symbols.',
                    messageTemplate: null,
                    parameters: [$this->dto->password],
                    root: 'password',
                    propertyPath: 'password',
                    invalidValue: [$this->dto->password]
                ));
            }
        }

        return $list;
    }
}