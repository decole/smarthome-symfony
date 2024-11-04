<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\Sensor;

use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SensorCrudValidationService implements ValidationInterface
{
    private CrudSensorDto $dto;

    public function __construct(private ValidatorInterface $validator, private SensorRepositoryInterface $repository)
    {
    }

    public function validate(bool $isUpdate = false): ConstraintViolationListInterface
    {
        $list = $this->validator->validate($this->dto);

        assert($list instanceof ConstraintViolationList);

        if ($this->dto->name === null || $this->dto->topic === null) {
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
        if ($this->repository->findByName($this->dto->name) instanceof \App\Domain\Sensor\Entity\Sensor) {
            $list->add(new ConstraintViolation(
                message: 'Sensor name already exist.',
                messageTemplate: null,
                parameters: [$this->dto->name],
                root: 'name',
                propertyPath: 'name',
                invalidValue: $this->dto->name
            ));
        }

        if ($this->repository->findByTopic($this->dto->topic) instanceof \App\Domain\Sensor\Entity\Sensor) {
            $list->add(new ConstraintViolation(
                message: 'Sensor topic already exist.',
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