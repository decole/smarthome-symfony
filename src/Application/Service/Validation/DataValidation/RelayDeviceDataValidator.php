<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Domain\Doctrine\Relay\Entity\Relay;

final class RelayDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidatedDto
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
