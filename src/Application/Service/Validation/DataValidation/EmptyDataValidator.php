<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\EmptyDevice\Entity\EmptyDevice;
use App\Domain\Payload\Entity\DevicePayload;

final readonly class EmptyDataValidator implements DeviceDataValidatorInterface
{
    public function __construct(private DevicePayload $payload) {}

    public function handle(): DeviceDataValidatedDto
    {
        return new DeviceDataValidatedDto(
            devicePayload: $this->payload,
            device: new EmptyDevice(),
            hasCheckStatusWarning: false,
            hasAlertingNotify: false,
        );
    }
}
