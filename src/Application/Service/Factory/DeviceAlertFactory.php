<?php

declare(strict_types=1);

namespace App\Application\Service\Factory;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Notification\Criteria\CriteriaInterface;
use App\Domain\Notification\Criteria\FireSecureCriteria;
use App\Domain\Notification\Criteria\RelayCriteria;
use App\Domain\Notification\Criteria\SecurityCriteria;
use App\Domain\Notification\Criteria\SensorCriteria;
use App\Domain\Payload\Entity\DevicePayload;
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