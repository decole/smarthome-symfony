<?php

declare(strict_types=1);

namespace App\Application\Service\Factory;

use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Notification\Criteria\CriteriaInterface;
use App\Domain\Notification\Criteria\FireSecureCriteria;
use App\Domain\Notification\Criteria\RelayCriteria;
use App\Domain\Notification\Criteria\SecurityCriteria;
use App\Domain\Notification\Criteria\SensorCriteria;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class DeviceAlertFactory
{
    public function __construct(private EventDispatcherInterface $eventDispatcher) {}

    public function create(DeviceDataValidatedDto $dto): CriteriaInterface
    {
        return match ($dto->device::alias()) {
            Security::alias() => (new SecurityCriteria($this->eventDispatcher, $dto)),
            FireSecurity::alias() => (new FireSecureCriteria($this->eventDispatcher, $dto)),
            Sensor::alias() => (new SensorCriteria($this->eventDispatcher, $dto)),
            Relay::alias() => (new RelayCriteria($this->eventDispatcher, $dto)),
        };
    }
}
