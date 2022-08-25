<?php

namespace App\Application\Http\Web\Relay\Dto;

use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Doctrine\Relay\Entity\Relay;
use Symfony\Component\Validator\Constraints as Assert;

class CrudRelayDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(choices: Relay::RELAY_TYPES)]
    public ?string $type = Relay::DRY_RELAY_TYPE;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $topic = null;
    public ?string $payload = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $commandOn = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $commandOff = null;

    public ?string $isFeedbackPayload = null;
    public ?string $checkTopic = null;
    public ?string $checkTopicPayloadOn = null;
    public ?string $checkTopicPayloadOff = null;
    public ?string $lastCommand = null;

    public ?string $message_info = null;
    public ?string $message_ok = null;
    public ?string $message_warn = null;

    public ?string $status = null;
    public ?string $notify = null;
}