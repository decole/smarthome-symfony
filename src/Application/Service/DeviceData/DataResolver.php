<?php

namespace App\Application\Service\DeviceData;

use App\Domain\Contract\Repository\EntityInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Application\Service\Alert\AlertService;
use App\Domain\Payload\DevicePayload;

/**
 * Сервис работы с данными устройств (проверка состояния и вызов оповещения)
 */
final class DataResolver
{
    public function __construct(
        private DataValidationService $validateService,
        private DeviceDataCacheService $cacheService,
        private AlertService $alertService
    ) {
    }

    public function resolveDevicePayload(DevicePayload $payload): void
    {
        $this->cacheService->save($payload);
        $resultDto = $this->validateService->validate($payload);

        if (!$resultDto->isValid()) {
            /** @var Sensor|Relay|Security|FireSecurity $device */
            $device = $resultDto->getDevice();
            $deviceValue = $payload->getPayload();
            $this->alertService->messengerNotify($this->prepareDeviceAlert($device, $deviceValue));
        }
    }

    /**
     * @param EntityInterface $device
     * @param string $payload
     * @return string
     */
    public function prepareDeviceAlert(EntityInterface $device, string $payload): string
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
}