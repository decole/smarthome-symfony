<?php

namespace App\Domain\FireSecurity\EventListener;

use App\Domain\Event\EntityListEvent;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Infrastructure\Repository\FireSecurity\FireSecurityRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: EntityListEvent::NAME, method: 'onSensorEntityListEvent')]
final class FireSecurityEntityListEventListener
{
    public function __construct(private readonly FireSecurityRepository $repository)
    {
    }

    public function onSensorEntityListEvent(EntityListEvent $event): void
    {
        $event->setEntityMapByType(
            type: FireSecurity::alias(),
            entities: $this->repository->findAll()
        );
    }
}