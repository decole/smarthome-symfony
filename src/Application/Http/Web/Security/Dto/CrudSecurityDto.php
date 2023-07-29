<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Security\Dto;

use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Security\Entity\Security;
use Symfony\Component\Validator\Constraints as Assert;

class CrudSecurityDto implements ValidationDtoInterface
{
    /**
     * @see App\Domain\Security\Enum\SecurityTypeEnum
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $type = 'mqtt_security_device';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $topic = null;
    public ?string $payload = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $detectPayload = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $holdPayload = null;

    public ?string $lastCommand = null;

    public ?string $params = null;

    public ?string $message_info = null;
    public ?string $message_ok = null;
    public ?string $message_warn = null;

    public ?string $status = null;
    public ?string $notify = null;
}