<?php

namespace App\Application\Service\Validation\FireSecurity;

use App\Application\Http\Web\FireSecurity\Dto\CrudFireSecurityDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FireSecurityValidationService implements ValidationInterface
{
    private CrudFireSecurityDto $dto;

    public function __construct(private ValidatorInterface $validator, private FireSecurityRepositoryInterface $repository)
    {
    }

    public function validate(bool $isUpdate): ConstraintViolationListInterface
    {
        $list = $this->validator->validate($this->dto);

        assert($list instanceof ConstraintViolationList);

        if (count($list) > 0) {
            return $list;
        }

        if (!$isUpdate) {
            $list = $this->uniqueValidate($list);
        }

        return $list;
    }

    public function setValue(ValidationDtoInterface $dto): void
    {
        $this->dto = $dto;
    }

    private function uniqueValidate(ConstraintViolationList $list): ConstraintViolationListInterface
    {
        if ($this->repository->findByName($this->dto->name)) {
            $list->add(new ConstraintViolation(
                message: 'Fire security device name already exist.',
                messageTemplate: null,
                parameters: [$this->dto->name],
                root: 'name',
                propertyPath: 'name',
                invalidValue: $this->dto->name
            ));
        }

        if ($this->repository->findByTopic($this->dto->topic)) {
            $list->add(new ConstraintViolation(
                message: 'Fire security device topic already exist.',
                messageTemplate: null,
                parameters: [$this->dto->topic],
                root: 'topic',
                propertyPath: 'topic',
                invalidValue: $this->dto->topic
            ));
        }

        return $list;
    }
}
