<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Plc\Dto;

use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CrudPlcDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $targetTopic = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    public ?int $alarmSecondDelay = 60;

    public ?string $message_info = null;
    public ?string $message_ok = null;
    public ?string $message_warn = null;

    public ?string $status = null;
    public ?string $notify = null;
    public ?string $savedId = null;
}