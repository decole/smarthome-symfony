<?php


namespace App\Application\Service\Validation\Relay;


use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Service\Validation\ValidationDtoInterface;
use App\Application\Service\Validation\ValidationInterface;
use App\Infrastructure\Doctrine\Interfaces\RelayRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RelayValidationService implements ValidationInterface
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
                message: 'Sensor name already exist.',
                messageTemplate: null,
                parameters: [$this->dto->name],
                root: 'name',
                propertyPath: 'name',
                invalidValue: $this->dto->name
            ));
        }

        if ($this->repository->findByTopic($this->dto->topic)) {
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