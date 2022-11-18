<?php

namespace App\Application\Http\Web\Profile\Dto;

use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CrudProfileDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $login = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\Type(type: 'integer')]
    public ?int $telegramId = null;

    public ?string $isChangePassword = null;

    public ?string $password = null;

    public ?string $passwordAgan = null;
}