<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\Page;

use App\Application\Http\Web\Page\Dto\CrudPageDto;
use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\PageRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PageCrudValidationService implements ValidationInterface
{
    /**
     * @var CrudPageDto
     */
    private ValidationDtoInterface $dto;

    public function __construct(private ValidatorInterface $validator, private PageRepositoryInterface $repository)
    {
    }

    public function validate(bool $isUpdate): ConstraintViolationListInterface
    {
        $list = $this->validator->validate($this->dto);

        assert($list instanceof ConstraintViolationList);

        if ($this->dto->name === null || $this->dto->config === null) {
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
        if ($this->repository->findByName($this->dto->name) instanceof \App\Domain\Page\Entity\Page) {
            $list->add(new ConstraintViolation(
                message: 'Page name already exist.',
                messageTemplate: null,
                parameters: [$this->dto->name],
                root: 'name',
                propertyPath: 'name',
                invalidValue: $this->dto->name
            ));
        }

        return $list;
    }
}