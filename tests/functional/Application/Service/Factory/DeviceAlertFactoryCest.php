<?php

namespace App\Tests\functional\Application\Service\Factory;

use App\Application\Service\Factory\DeviceAlertFactory;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Notification\Criteria\FireSecureCriteria;
use App\Domain\Notification\Criteria\RelayCriteria;
use App\Domain\Notification\Criteria\SecurityCriteria;
use App\Domain\Notification\Criteria\SensorCriteria;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use App\Tests\_support\Step\FunctionalStep\Application\Service\Factory\DeviceDataValidationFactoryStep;
use Codeception\Stub;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeviceAlertFactoryCest
{
    // todo сделать проверку нотификаций

    private Sensor $sensor;
    private Relay $relay;
    private Security $security;
    private FireSecurity $fireSecure;

    public function _before(DeviceDataValidationFactoryStep $I): void
    {
        [$this->sensor, $this->relay, $this->security, $this->fireSecure] = $I->createDeviceByTypes();
    }

    public function createSensorCriteria(DeviceDataValidationFactoryStep $I): void
    {
        $eventDispatcher = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => fn () => (object)[]]
        );

        $factory = new DeviceAlertFactory($eventDispatcher);

        $criteria = $factory->create($this->sensor, new DevicePayload($this->sensor->getPayload(), 0));

        $I->assertInstanceOf(SensorCriteria::class, $criteria);
    }

    public function createRelayCriteria(DeviceDataValidationFactoryStep $I): void
    {
        $eventDispatcher = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => fn () => (object)[]]
        );

        $factory = new DeviceAlertFactory($eventDispatcher);

        $criteria = $factory->create($this->relay, new DevicePayload($this->relay->getPayload(), 0));

        $I->assertInstanceOf(RelayCriteria::class, $criteria);
    }

    public function createSecureCriteria(DeviceDataValidationFactoryStep $I): void
    {
        $eventDispatcher = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => fn () => (object)[]]
        );

        $factory = new DeviceAlertFactory($eventDispatcher);

        $criteria = $factory->create($this->security, new DevicePayload($this->security->getPayload(), 0));

        $I->assertInstanceOf(SecurityCriteria::class, $criteria);
    }

    public function createFireSecureCriteria(DeviceDataValidationFactoryStep $I): void
    {
        $eventDispatcher = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => fn () => (object)[]]
        );

        $factory = new DeviceAlertFactory($eventDispatcher);

        $criteria = $factory->create($this->fireSecure, new DevicePayload($this->fireSecure->getPayload(), 0));

        $I->assertInstanceOf(FireSecureCriteria::class, $criteria);
    }
}