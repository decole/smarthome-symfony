<?php


namespace App\Application\Http\Web\Sensor\Dto;


use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use Symfony\Component\Validator\Constraints as Assert;

class CrudSensorDto implements ValidationDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: Sensor::SENSOR_TYPES)]
    public ?string $type = null;

    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $topic = null;

    public ?string $payload = null;

    public ?string $payload_min = null;
    public ?string $payload_max = null;
    public ?string $payload_dry = null;
    public ?string $payload_wet = null;
    public ?string $payload_high = null;
    public ?string $payload_low = null;

    public ?string $message_info = null;
    public ?string $message_ok = null;
    public ?string $message_warn = null;

    public ?string $status = null;
    public ?string $notify = null;
}