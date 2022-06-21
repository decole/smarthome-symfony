<?php


namespace App\Application\Service\Validation;


use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationInterface
{
    public function validate(bool $isUpdate): ConstraintViolationListInterface;

    public function setValue(ValidationDtoInterface $dto): void;
}