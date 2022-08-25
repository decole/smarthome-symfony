<?php

namespace App\Application\Http\Web\Security\Dto;

use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Doctrine\Security\Entity\Security;
use Symfony\Component\Validator\Constraints as Assert;

class CrudSecurityDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(choices: Security::SECURITY_TYPES)]
    public ?string $type = Security::MQTT_TYPE;

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

    public array $params = [];

    public ?string $message_info = null;
    public ?string $message_ok = null;
    public ?string $message_warn = null;

    public ?string $status = null;
    public ?string $notify = null;
}