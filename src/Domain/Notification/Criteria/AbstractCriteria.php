<?php

declare(strict_types=1);

namespace App\Domain\Notification\Criteria;

use App\Domain\DeviceData\Entity\DeviceDataValidatedDto;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Event\VisualNotificationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

abstract class AbstractCriteria implements CriteriaInterface
{
    protected const array MAP = [
        '{value}',
        '%s',
    ];

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected DeviceDataValidatedDto $dto,
    ) {}

    abstract public function prepareAlertMessage(): string;

    public function notify(): void
    {
        if ($this->dto->hasAlertingNotify) {
            $message = $this->generateAlertMessage();

            $this->sendByVisualNotify($message);
            $this->sendByMessengers($message);
        }

        if ($this->dto->hasCheckStatusWarning) {
            $message = $this->generateStatusMessage();

            $this->sendByMessengers($message);
        }
    }

    final public function generateAlertMessage(): string
    {
        return str_replace(self::MAP, $this->dto->devicePayload->getPayload(), $this->prepareAlertMessage());
    }

    final public function generateStatusMessage(): string
    {
        return str_replace(self::MAP, $this->dto->devicePayload->getPayload(), $this->prepareStatusMessage());
    }

    final public function prepareStatusMessage(): string
    {
        $text = $this->dto->device->getStatusMessage()->getMessageInfo() ?? null;

        if (empty($text)) {
            $text = $this->dto->device->getName();
        }

        return \sprintf(
            '%s: %s',
            $text,
            'Обнаружено несоответствие. Активна настройка сравнения данных (Статус).',
        );
    }

    final public function sendByVisualNotify(string $text): void
    {
        $event = new VisualNotificationEvent($text, $this->dto->device);

        $this->eventDispatcher->dispatch($event, VisualNotificationEvent::NAME);
    }

    final public function sendByMessengers(string $text): void
    {
        $event = new AlertNotificationEvent($text, [AlertNotificationEvent::MESSENGER]);

        $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
    }
}
