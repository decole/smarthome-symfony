<?php

namespace App\Application\Service\DeviceData;

use App\Application\Service\DeviceData\Dto\SecureDeviceStateDto;
use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Security\Entity\Security;
use App\Infrastructure\Doctrine\Repository\Security\SecurityRepository;
use Doctrine\ORM\NonUniqueResultException;

class SecureDeviceDataService
{
    public function __construct(
        private DeviceCacheService $deviceService,
        private DeviceDataCacheService $dataCacheService,
        private SecurityRepository $repository,
        private TransactionInterface $transaction
    ) {
    }

    /**
     * статус датчика - state
     * true - движение/сработка
     * false - нет движения
     *
     * (статус охранного датчика) - isTriggered:
     * true - взведен
     * false - не взведен
     */
    public function getDeviceState(string $topic): SecureDeviceStateDto
    {
        $dto = new SecureDeviceStateDto();
        $targetDevice = null;
        $map = $this->deviceService->getDeviceMap()[Security::alias()];

        /** @var Security $device */
        foreach ($map ?? [] as $device) {
            if ($device->getTopic() === $topic) {
                $targetDevice = $device;
            }
        }

        if ($targetDevice === null) {
            return $dto;
        }

        $payload = $this->dataCacheService->getPayloadByTopicList([$topic])[$device->getTopic()] ?? null;

        $dto->standardisedState = $payload === (string)$device->getDetectPayload();
        $dto->isGuarded = $device->isGuarded();

        return $dto;
    }

    /**
     * Сохранение нового состояния устройства безопасности из виджета безопасности
     *
     * @param string $topic
     * @param bool $trigger
     * @return void
     * @throws NonUniqueResultException
     */
    public function setTrigger(string $topic, bool $trigger): void
    {
        $device = $this->repository->findByTopic($topic);

        if ($device === null) {
            return;
        }

        $device->setGuardState($trigger === true ? Security::GUARD_STATE : Security::HOLD_STATE);

        $this->transaction->transactional(
            function () use ($device) {
                $this->repository->save($device);
            }
        );

        $this->deviceService->create();
    }
}