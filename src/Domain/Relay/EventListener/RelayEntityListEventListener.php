<?php

namespace App\Domain\Relay\EventListener;

use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Event\EntityListEvent;
use App\Domain\Relay\Entity\Relay;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: EntityListEvent::NAME, method: 'onRelayEntityListEvent')]
class RelayEntityListEventListener
{
    public function __construct(private readonly RelayRepositoryInterface $repository)
    {
    }

    public function onRelayEntityListEvent(EntityListEvent $event): void
    {
        $event->setEntityMapByType(
            type: Relay::alias(),
            entities: $this->repository->findAll()
        );
    }
}