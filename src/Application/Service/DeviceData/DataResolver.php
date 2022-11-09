<?php

namespace App\Application\Service\DeviceData;

use App\Application\Service\DeviceData\Dto\DeviceDataValidatedDto;
use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Event\VisualNotificationEvent;
use App\Domain\Payload\DevicePayload;
use Psr\EventDispatcher\EventDispatcherInterface;

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

    public function resolveDevicePayload(DevicePayload $payload): void
    {
        $this->cacheService->save($payload);
        $this->canBeNotify($payload);
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

    /**
     * @param DevicePayload $payload
     * @return void
     */
    public function canBeNotify(DevicePayload $payload): void
    {
        $resultDto = $this->validateService->validate($payload);

        /** @var Sensor|Relay|Security|FireSecurity $device */
        $device = $resultDto->getDevice();

        // охранный датчик в состоянии "движение" и он взеден и выставлен флаг оповещения через мессенджеры
        if ($device instanceof Security && $device->isNotify() && $device->isGuarded()) {
            $this->notification($resultDto, $payload);

            return;
        }

        if (!$resultDto->isNormal() && $device->isNotify()) {
            $this->notification($resultDto, $payload);
        }
    }
}