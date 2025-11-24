<?php

declare(strict_types=1);

namespace App\Domain\DeviceData\Service;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\Factory\DeviceAlertFactory;
use App\Application\Service\Factory\DeviceDataValidationFactory;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Payload\Entity\DevicePayload;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Сервис работы с данными устройств (проверка состояния и вызов оповещения).
 */
final class DeviceDataResolver
{
    private bool $isInterrupt = false;

    public function __construct(
        private readonly DeviceCacheService $service,
        private readonly DeviceDataCacheService $deviceDataCacheService,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @throws InvalidArgumentException|CacheException
     */
    public function resolveDevicePayload(DevicePayload $payload): void
    {
        if ($this->isInterrupt) {
            DeviceDataException::processInterrupted();
        }

        $this->deviceDataCacheService->save($payload);

        $this->execute($payload);
    }

    public function setInterrupt(): void
    {
        $this->isInterrupt = true;
    }

    private function execute(DevicePayload $payload): void
    {
        try {
            $this->validatePayload($payload);
        } catch (\Throwable $e) {
            $event = new AlertNotificationEvent($e->getMessage(), [AlertNotificationEvent::MESSENGER]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }
    }

    /**
     * @throws DeviceDataException
     * @throws InvalidArgumentException
     */
    private function validatePayload(DevicePayload $payload): void
    {
        $device = $this->findDevice($payload);

        if (null === $device) {
            return;
        }

        $validator = (new DeviceDataValidationFactory())->create($device, $payload);

        $resultDto = $validator->handle();

        if ($resultDto->hasNotify()) {
            (new DeviceAlertFactory($this->eventDispatcher))
                ->create($resultDto)
                ->notify();
        }
    }

    /**
     * @return Sensor|Relay|FireSecurity|Security|null
     *
     * @throws InvalidArgumentException
     */
    private function findDevice(DevicePayload $payload): ?EntityInterface
    {
        $map = $this->service->getTopicMapByDeviceTopic();

        return $map[$payload->getTopic()] ?? null;
    }
}
