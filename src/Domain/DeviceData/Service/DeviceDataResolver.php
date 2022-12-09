<?php

namespace App\Domain\DeviceData\Service;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\Factory\DeviceAlertFactory;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Payload\Entity\DevicePayload;
use Psr\Cache\InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Сервис работы с данными устройств (проверка состояния и вызов оповещения)
 */
final class DeviceDataResolver
{
    public function __construct(
        private DeviceDataValidationService $validateService,
        private DeviceDataCacheService $cacheService,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function resolveDevicePayload(DevicePayload $payload): void
    {
        $this->cacheService->save($payload);
        $this->execute($payload);
    }

    private function execute(DevicePayload $payload): void
    {
        try {
            $this->validatePayload($payload);
        } catch (Throwable $e) {
            $event = new AlertNotificationEvent($e->getMessage(), [
                AlertNotificationEvent::MESSENGER,
            ]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }
    }

    /**
     * @throws DeviceDataException
     * @throws InvalidArgumentException
     */
    private function validatePayload(DevicePayload $payload): void
    {
        $resultDto = $this->validateService->validate($payload);

        if (!$resultDto->isNormal()) {
            (new DeviceAlertFactory($this->eventDispatcher))
                ->create($resultDto->getDevice(), $payload)
                ->notify();
        }
    }
}