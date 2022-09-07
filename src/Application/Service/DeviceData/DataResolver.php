<?php

namespace App\Application\Service\DeviceData;

use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
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
        private AlertService $alertService,
        private UserRepository $repository
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
            $alertMessage = $this->alertService
                ->prepareMessage($device, $deviceValue);

            foreach ($this->repository->findAllWithTelegramId() as $user) {
                $this->alertService->userNotify($user, $alertMessage);
            }
        }
    }
}
