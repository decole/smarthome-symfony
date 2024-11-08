<?php

declare(strict_types=1);

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
    public string $alias;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $icon;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public int $groupId;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public array $config;
}