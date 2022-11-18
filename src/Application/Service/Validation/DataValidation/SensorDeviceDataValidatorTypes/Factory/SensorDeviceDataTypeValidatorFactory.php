<?php

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\Factory;

use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\DryContactSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\HumiditySensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\LeakageSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\PressureSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\TemperatureSensorTypeValidator;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\Sensor\Entity\DryContactSensor;
use App\Domain\Doctrine\Sensor\Entity\HumiditySensor;
use App\Domain\Doctrine\Sensor\Entity\LeakageSensor;
use App\Domain\Doctrine\Sensor\Entity\PressureSensor;
use App\Domain\Doctrine\Sensor\Entity\TemperatureSensor;

class SensorDeviceDataTypeValidatorFactory
{
    public function create(EntityInterface $device)
    {
        return match (get_class($device)) {
            TemperatureSensor::class => new TemperatureSensorTypeValidator($device),
            HumiditySensor::class => new HumiditySensorTypeValidator($device),
            PressureSensor::class => new PressureSensorTypeValidator($device),
            LeakageSensor::class => new LeakageSensorTypeValidator($device),
            DryContactSensor::class => new DryContactSensorTypeValidator($device),
        };
    }
}