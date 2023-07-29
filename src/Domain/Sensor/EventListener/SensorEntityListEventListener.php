<?php

declare(strict_types=1);

namespace App\Domain\Sensor\EventListener;

use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Event\EntityListEvent;
use App\Domain\Sensor\Entity\Sensor;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: EntityListEvent::NAME, method: 'onSensorEntityListEvent')]
final class SensorEntityListEventListener
{
    public function __construct(private readonly SensorRepositoryInterface $repository)
    {
    }

    public function onSensorEntityListEvent(EntityListEvent $event): void
    {
        $event->setEntityMapByType(
            type: Sensor::alias(),
            entities: $this->repository->findAll()
        );
    }
}
