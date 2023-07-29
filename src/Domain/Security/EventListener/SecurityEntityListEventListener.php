<?php

declare(strict_types=1);

namespace App\Domain\Security\EventListener;

use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Event\EntityListEvent;
use App\Domain\Security\Entity\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: EntityListEvent::NAME, method: 'onSecureEntityListEvent')]
class SecurityEntityListEventListener
{
    public function __construct(private readonly SecurityRepositoryInterface $repository)
    {
    }

    public function onSecureEntityListEvent(EntityListEvent $event): void
    {
        $event->setEntityMapByType(
            type: Security::alias(),
            entities: $this->repository->findAll()
        );
    }
}