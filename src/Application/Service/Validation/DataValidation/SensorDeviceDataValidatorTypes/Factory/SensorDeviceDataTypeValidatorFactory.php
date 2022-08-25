<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\Factory;

use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\DryContactSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\HumiditySensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\LeakageSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\PressureSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\TemperatureSensorTypeValidator;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\Sensor\Entity\SensorDryContact;
use App\Domain\Doctrine\Sensor\Entity\SensorHumidity;
use App\Domain\Doctrine\Sensor\Entity\SensorLeakage;
use App\Domain\Doctrine\Sensor\Entity\SensorPressure;
use App\Domain\Doctrine\Sensor\Entity\SensorTemperature;

class SensorDeviceDataTypeValidatorFactory
{
    public function create(EntityInterface $device)
    {
        return match (get_class($device)) {
            SensorTemperature::class => new TemperatureSensorTypeValidator($device),
            SensorHumidity::class => new HumiditySensorTypeValidator($device),
            SensorPressure::class => new PressureSensorTypeValidator($device),
            SensorLeakage::class => new LeakageSensorTypeValidator($device),
            SensorDryContact::class => new DryContactSensorTypeValidator($device),
        };
    }
}
