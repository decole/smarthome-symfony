<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\DeviceData\Entity\DeviceDataValidated;
use App\Domain\Relay\Entity\Relay;

final class RelayDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidated
    {
        assert($this->device instanceof Relay);

        $payload = $this->payload->getPayload();

        if ($payload !== (string)$this->device->getCheckTopicPayloadOn() &&
            $payload !== (string)$this->device->getCheckTopicPayloadOff()
        ) {
            return $this->createDto(false, $this->device);
        }

        return $this->createDto(true, $this->device);
    }
}