<?php

namespace App\Application\Service\Validation\DataValidation;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Domain\Contract\Service\Validation\DataValidation\DeviceDataValidatorInterface;
use App\Domain\Doctrine\Security\Entity\Security;

final class SecurityDeviceDataValidator extends AbstractDeviceDataValidator implements DeviceDataValidatorInterface
{
    public function validate(): DeviceDataValidatedDto
    {
        assert($this->device instanceof Security);

        $alertState = $this->payload->getPayload() === (string)$this->device->getDetectPayload() &&
            $this->device->isGuarded() && $this->device->isNotify();

        // true - нормальное состояние
        // false - обнаружено движение и включено оповещение и взведено на охрану
        return $this->createDto(!$alertState, $this->device);
    }
}