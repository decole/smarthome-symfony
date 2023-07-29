<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Sensor\Dto;

use App\Domain\Contract\CrudValidation\ValidationDtoInterface;
use App\Domain\Sensor\Entity\Sensor;
use Symfony\Component\Validator\Constraints as Assert;

class CrudSensorDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(choices: Sensor::SENSOR_TYPES)]
    public ?string $type = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public ?string $topic = null;

    public ?string $payload = null;

    public ?string $payloadMin = null;
    public ?string $payloadMax = null;
    public ?string $payloadDry = null;
    public ?string $payloadWet = null;
    public ?string $payloadHigh = null;
    public ?string $payloadLow = null;

    public ?string $message_info = null;
    public ?string $message_ok = null;
    public ?string $message_warn = null;

    public ?string $status = null;
    public ?string $notify = null;
}