<?php

namespace App\Application\Service\Alert\Criteria;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Event\VisualNotificationEvent;
use App\Domain\Payload\DevicePayload;
use Psr\EventDispatcher\EventDispatcherInterface;

abstract class AbstractCriteria implements CriteriaInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected EntityInterface $device,
        protected DevicePayload $payload
    ) {
    }

    abstract public function notify(): void;

    abstract public function prepareAlertMessage(): string;

    final public function generateAlertMessage(): string
    {
        $search = [
            '{value}',
            '%s'
        ];

        return str_replace($search, $this->payload->getPayload(), $this->prepareAlertMessage());
    }

    final public function sendByVisualNotify(): void
    {
        $event = new VisualNotificationEvent($this->generateAlertMessage(), $this->device);
        $this->eventDispatcher->dispatch($event, VisualNotificationEvent::NAME);
    }

    final public function sendByMessengers(): void
    {
        $event = new AlertNotificationEvent($this->generateAlertMessage(), [AlertNotificationEvent::MESSENGER]);
        $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
    }
}