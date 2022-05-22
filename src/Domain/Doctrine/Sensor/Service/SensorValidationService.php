<?php


namespace App\Domain\Doctrine\Sensor\Service;


use App\Domain\Contract\Device\ValidationDeviceService;
use App\Domain\Payload\Dto\MessageDto;

class SensorValidationService implements ValidationDeviceService
{
    public function validate(MessageDto $message): bool
    {
        return true;
    }
}