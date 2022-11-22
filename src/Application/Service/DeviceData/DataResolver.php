<?php

namespace App\Application\Service\DeviceData;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Application\Service\Factory\DeviceAlertFactory;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Event\VisualNotificationEvent;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Payload\DevicePayload;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use Psr\Cache\InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Сервис работы с данными устройств (проверка состояния и вызов оповещения)
 */
final class DataResolver
{
    public function __construct(
        private DataValidationService $validateService,
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

    public function execute(DevicePayload $payload): void
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
            dump('is notify');

            $criteria = (new DeviceAlertFactory($this->eventDispatcher))->create($resultDto->getDevice(), $payload);

            dump(get_class($criteria));

            $criteria->notify();
        }
    }
}