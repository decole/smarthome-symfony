<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\Relay;

use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RelayCrudValidationService implements ValidationInterface
{
    private CrudRelayDto $dto;

    public function __construct(private ValidatorInterface $validator, private RelayRepositoryInterface $repository)
    {
    }

    public function validate(bool $isUpdate): ConstraintViolationListInterface
    {
        $list = $this->validator->validate($this->dto);

        assert($list instanceof ConstraintViolationList);

        $list = $this->checkIsFeedbackLogic($list);

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
        if ($this->repository->findByName($this->dto->name) instanceof \App\Domain\Relay\Entity\Relay) {
            $list->add(new ConstraintViolation(
                message: 'Relay name already exist.',
                messageTemplate: null,
                parameters: [$this->dto->name],
                root: 'name',
                propertyPath: 'name',
                invalidValue: $this->dto->name
            ));
        }

        if ($this->repository->findByTopic($this->dto->topic) instanceof \App\Domain\Relay\Entity\Relay) {
            $list->add(new ConstraintViolation(
                message: 'Relay topic already exist.',
                messageTemplate: null,
                parameters: [$this->dto->topic],
                root: 'topic',
                propertyPath: 'topic',
                invalidValue: $this->dto->topic
            ));
        }

        return $list;
    }

    private function checkIsFeedbackLogic(ConstraintViolationList $list): ConstraintViolationListInterface
    {
        if (
            $this->dto->isFeedbackPayload === 'on' &&
            (
                $this->dto->checkTopic === null ||
                $this->dto->checkTopicPayloadOn === null ||
                $this->dto->checkTopicPayloadOff === null
            )
        ) {
            $list->add(new ConstraintViolation(
                message: 'Relay feedback logic enabled and set check topic feedback params.',
                messageTemplate: null,
                parameters: [$this->dto->topic],
                root: 'feedback',
                propertyPath: 'feedback',
                invalidValue: null
            ));
        }

        return $list;
    }
}