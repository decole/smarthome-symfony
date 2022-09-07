<?php

namespace App\Application\Http\Web\Page\Dto;

use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CrudPageDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public array $config;
}
