<?php

namespace App\Application\Service\DeviceData;

use App\Application\Exception\DeviceDataException;
use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
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
     * @param EntityInterface $device
     * @param string $payload
     * @return string
     */
    private function prepareDeviceAlert(EntityInterface $device, string $payload): string
    {
        /** @var Sensor|Relay|Security|FireSecurity $device */
        $deviceAlertMessage = $device?->getStatusMessage()?->getMessageWarn();

        if ($deviceAlertMessage === null) {
            return sprintf("Внимание! {$device->getName()} имеет состояние: %s", $payload);
        }

        $search = [
            '{value}',
            '%s'
        ];

        return str_replace($search, $payload, $deviceAlertMessage);
    }

    /**
     * @throws DeviceDataException
     * @throws InvalidArgumentException
     */
    private function validatePayload(DevicePayload $payload): void
    {
        $resultDto = $this->validateService->validate($payload);

        /** @var Sensor|Relay|Security|FireSecurity $device */
        $device = $resultDto->getDevice();

        // todo плохо пахнет.
        // охранный датчик в состоянии "движение" и он взеден и выставлен флаг оповещения через мессенджеры
        if ($device::alias() === Security::alias() && $device->isNotify() && $device->isGuarded()) {
            $this->notification($resultDto, $payload);

            return;
        }

        if ($device::alias() !== Security::alias() && !$resultDto->isNormal() && $device->isNotify()) {

            $this->notification($resultDto, $payload);
        }
    }

    private function notification(DeviceDataValidatedDto $resultDto, DevicePayload $payload): void
    {
        /** @var Sensor|Relay|Security|FireSecurity $device */
        $device = $resultDto->getDevice();
        $deviceValue = $payload->getPayload();
        $message = $this->prepareDeviceAlert($device, $deviceValue);

        $event = new VisualNotificationEvent($message, $device);
        $this->eventDispatcher->dispatch($event, VisualNotificationEvent::NAME);

        $event = new AlertNotificationEvent($message, [AlertNotificationEvent::MESSENGER]);
        $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
    }
}