<?php

namespace App\Tests\functional\Application\Service\Factory;

use App\Application\Service\Factory\DeviceDataValidationFactory;
use App\Application\Service\Validation\DataValidation\EmptyDataValidator;
use App\Application\Service\Validation\DataValidation\FireSecurityDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\RelayDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\SecurityDeviceDataValidator;
use App\Application\Service\Validation\DataValidation\SensorDeviceDataValidator;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use App\Tests\_support\Step\FunctionalStep\Application\Service\Factory\DeviceDataValidationFactoryStep;

class DeviceDataValidationFactoryCest
{
    private ?DeviceDataValidationFactory $factory = null;
    private Sensor $sensor;
    private Relay $relay;
    private Security $security;
    private FireSecurity $fireSecure;

    public function _before(DeviceDataValidationFactoryStep $I): void
    {
        [$this->sensor, $this->relay, $this->security, $this->fireSecure] = $I->createDeviceByTypes();

        $this->factory = new DeviceDataValidationFactory([
            $this->sensor->getTopic() => $this->sensor,

            $this->relay->getTopic() => $this->relay,
            $this->relay->getCheckTopic() => $this->relay,

            $this->security->getTopic() => $this->security,

            $this->fireSecure->getTopic() => $this->fireSecure,
        ]);
    }

    public function findDevice(DeviceDataValidationFactoryStep $I): void
    {
        $dto = new DevicePayload($this->sensor->getTopic(), $this->sensor->getPayload());
        $device = $this->factory->findDevice($dto);

        $I->assertInstanceOf(Sensor::class, $device);

        $dto = new DevicePayload($this->relay->getTopic(), 0);
        $device = $this->factory->findDevice($dto);

        $I->assertInstanceOf(Relay::class, $device);

        $dto = new DevicePayload($this->security->getTopic(), 0);
        $device = $this->factory->findDevice($dto);

        $I->assertInstanceOf(Security::class, $device);
    }

    public function findSensor(DeviceDataValidationFactoryStep $I): void
    {
        $dto = new DevicePayload($this->sensor->getTopic(), $this->sensor->getPayload());
        $validator = $this->factory->create($dto);

        $I->assertInstanceOf(SensorDeviceDataValidator::class, $validator);
    }

    public function findRelay(DeviceDataValidationFactoryStep $I): void
    {
        $dto = new DevicePayload($this->relay->getTopic(), 0);
        $validator = $this->factory->create($dto);

        $I->assertInstanceOf(RelayDeviceDataValidator::class, $validator);
    }

    public function findSecureDevice(DeviceDataValidationFactoryStep $I): void
    {
        $dto = new DevicePayload($this->security->getTopic(), 0);
        $validator = $this->factory->create($dto);

        $I->assertInstanceOf(SecurityDeviceDataValidator::class, $validator);
    }

    public function findFireSecureDevice(DeviceDataValidationFactoryStep $I): void
    {
        $dto = new DevicePayload($this->fireSecure->getTopic(), 0);
        $validator = $this->factory->create($dto);

        $I->assertInstanceOf(FireSecurityDeviceDataValidator::class, $validator);
    }

    public function getNotFoundDevice(DeviceDataValidationFactoryStep $I): void
    {
        $dto = new DevicePayload(null, null);
        $validator = $this->factory->create($dto);

        $I->assertInstanceOf(EmptyDataValidator::class, $validator);
    }
}