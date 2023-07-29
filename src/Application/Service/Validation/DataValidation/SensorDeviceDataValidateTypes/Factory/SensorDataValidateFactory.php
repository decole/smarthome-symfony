<?php

declare(strict_types=1);

namespace App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\Factory;

use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\DryContactSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\HumiditySensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\LeakageSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\PressureSensorTypeValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidateTypes\TemperatureSensorTypeValidator;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Contract\Service\Validation\DataValidation\SensorDeviceDataValidatorTypes\SensorTypeValidatorInterface;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Sensor\Entity\DryContactSensor;
use App\Domain\Sensor\Entity\HumiditySensor;
use App\Domain\Sensor\Entity\LeakageSensor;
use App\Domain\Sensor\Entity\PressureSensor;
use App\Domain\Sensor\Entity\TemperatureSensor;

final class SensorDataValidateFactory
{
    public function create(EntityInterface $device, DevicePayload $payload): SensorTypeValidatorInterface
    {
        return match (get_class($device)) {
            TemperatureSensor::class => new TemperatureSensorTypeValidator($device, $payload),
            HumiditySensor::class => new HumiditySensorTypeValidator($device, $payload),
            PressureSensor::class => new PressureSensorTypeValidator($device, $payload),
            LeakageSensor::class => new LeakageSensorTypeValidator($device, $payload),
            DryContactSensor::class => new DryContactSensorTypeValidator($device, $payload),
        };
    }
}