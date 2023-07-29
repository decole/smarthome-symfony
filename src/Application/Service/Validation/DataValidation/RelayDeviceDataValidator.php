<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Relay\Entity\Relay;

final class RelayDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    /**
     * @var Relay $device
     */

    /**
     * @return DeviceDataValidatedDto
     */
    public function handle(): DeviceDataValidatedDto
    {
        assert($this->device instanceof Relay);

        $payload = $this->payload->getPayload();

        if ($payload !== (string)$this->device->getCheckTopicPayloadOn() &&
            $payload !== (string)$this->device->getCheckTopicPayloadOff()
        ) {
            return $this->createDto(
                state: null,
                device: $this->device,
                isAlert: true
            );
        }

        return $this->createDto(state: true,
            device: $this->device,
            isAlert: false
        );
    }
}