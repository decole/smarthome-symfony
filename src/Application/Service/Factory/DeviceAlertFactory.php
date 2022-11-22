<?php

namespace App\Application\Service\Factory;

use App\Application\Service\Alert\Criteria\CriteriaInterface;
use App\Application\Service\Alert\Criteria\FireSecureCriteria;
use App\Application\Service\Alert\Criteria\RelayCriteria;
use App\Application\Service\Alert\Criteria\SecurityCriteria;
use App\Application\Service\Alert\Criteria\SensorCriteria;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Payload\DevicePayload;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use Psr\EventDispatcher\EventDispatcherInterface;

final class DeviceAlertFactory
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function create(EntityInterface $device, DevicePayload $payload): CriteriaInterface
    {
        return match ($device::alias()) {
            Security::alias() => (new SecurityCriteria($this->eventDispatcher, $device, $payload)),
            FireSecurity::alias() => (new FireSecureCriteria($this->eventDispatcher, $device, $payload)),
            Sensor::alias() => (new SensorCriteria($this->eventDispatcher, $device, $payload)),
            Relay::alias() => (new RelayCriteria($this->eventDispatcher, $device, $payload)),
        };
    }
}