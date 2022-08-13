<?php


namespace App\Application\Http\Web\FireSecurity\Dto;


use App\Application\Service\Validation\ValidationDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CrudFireSecurityDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $topic = null;
    public ?string $payload = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $normalPayload = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $alertPayload = null;

    public ?string $lastCommand = null;

    public ?string $message_info = null;
    public ?string $message_ok = null;
    public ?string $message_warn = null;

    public ?string $status = null;
    public ?string $notify = null;
}
