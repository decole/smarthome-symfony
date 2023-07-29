<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\PLC;

use App\Application\Http\Web\Plc\Dto\CrudPlcDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\PlcRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PlcCrudValidationService implements ValidationInterface
{
    private CrudPlcDto $dto;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly PlcRepositoryInterface $repository
    ) {
    }

    public function validate(bool $isUpdate): ConstraintViolationListInterface
    {
        $list = $this->validator->validate($this->dto);

        assert($list instanceof ConstraintViolationList);

        if ($this->dto->name === null || $this->dto->targetTopic === null) {
            return $list;
        }

        return $this->uniqueValidate($list, $isUpdate);
    }

    public function setValue(ValidationDtoInterface $dto): void
    {
        $this->dto = $dto;
    }

    private function uniqueValidate(ConstraintViolationList $list, bool $isUpdate): ConstraintViolationListInterface
    {
        if ($isUpdate) {
            $updatingEntity = $this->repository->findById($this->dto->savedId);
            $savedEntity = $this->repository->findByName($this->dto->name);

            if ($savedEntity !== null &&
                $updatingEntity!== null &&
                $updatingEntity->getIdToString() !== $savedEntity->getIdToString()
            ) {
                $list->add(new ConstraintViolation(
                    message: 'Plc name already exist to another entity.',
                    messageTemplate: null,
                    parameters: [$this->dto->name],
                    root: 'name',
                    propertyPath: 'name',
                    invalidValue: $this->dto->name
                ));
            }

            return $list;
        }

        if ($this->repository->findByName($this->dto->name)) {
            $list->add(new ConstraintViolation(
                message: 'Plc name already exist.',
                messageTemplate: null,
                parameters: [$this->dto->name],
                root: 'name',
                propertyPath: 'name',
                invalidValue: $this->dto->name
            ));
        }

        if ($this->repository->findByTargetTopic($this->dto->targetTopic)) {
            $list->add(new ConstraintViolation(
                message: 'Plc targetTopic already exist.',
                messageTemplate: null,
                parameters: [$this->dto->targetTopic],
                root: 'targetTopic',
                propertyPath: 'targetTopic',
                invalidValue: $this->dto->targetTopic
            ));
        }

        return $list;
    }
}