<?php

namespace App\Domain\PeriodicHandleCriteria\Criteria;

use App\Application\Service\DeviceData\DeviceCacheService;
use App\Application\Service\DeviceData\DeviceDataCacheService;
use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\PeriodicHandleCriteria\Exception\SaveDeviceStateException;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Cron\CronExpression;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Периодически сохраняет данные датчиков в базу данных.
 */
final class SaveDeviceStateCriteria implements PeriodicHandleCriteriaInterface
{
    private const RELAY = 'r';
    private const SENSOR = 's';
    private const SECURE = 'se';
    private const FIRE_SECURE = 'f';

    public function __construct(
        private DeviceDataCacheService $service,
        private DeviceCacheService $cacheService,
        private SensorRepositoryInterface $sensorRepository,
        private RelayRepositoryInterface $relayRepository,
        private SecurityRepositoryInterface $securityRepository,
        private FireSecurityRepositoryInterface $fireSecurityRepository,
        private TransactionInterface $transaction,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public static function alias(): string
    {
        return 'deviceStateSaver';
    }

    /**
     * Запускать каждые 5 минут
     *
     * @return bool
     */
    public function isDue(): bool
    {
        return (new CronExpression('*/5 * * * *'))->isDue();
    }

    public function execute(): void
    {
        try {
            $this->handle();
        } catch (Throwable $exception) {
            $this->alert($exception->getMessage());
        }
    }

    /**
     * @return void
     * @throws SaveDeviceStateException
     */
    private function handle(): void
    {
        $list = [];
        $devices = $this->cacheService->getTopicMapByDeviceTopic();

        foreach ($devices as $device) {
            if ($device instanceof Relay && $device->getCheckTopic() !== null) {
                $list[$device->getCheckTopic()] = [
                    'type' => self::RELAY,
                    'id' => $device->getId()->toString(),
                ];
            }
            if ($device instanceof Sensor) {
                $list[$device->getTopic()] = [
                    'type' => self::SENSOR,
                    'id' => $device->getId()->toString(),
                ];
            }
            if ($device instanceof Security) {
                $list[$device->getTopic()] = [
                    'type' => self::SECURE,
                    'id' => $device->getId()->toString(),
                ];
            }
            if ($device instanceof FireSecurity) {
                $list[$device->getTopic()] = [
                    'type' => self::SECURE,
                    'id' => $device->getId()->toString(),
                ];
            }
        }

        $payloadMap = $this->service->getPayloadByTopicList(array_keys($list));

        foreach ($payloadMap as $topic => $payload) {
            if (array_key_exists($topic, $list)) {
                $this->save($payload, $list[$topic]['type'], $list[$topic]['id']);
            }
        }
    }

    /**
     * @throws SaveDeviceStateException
     */
    private function save(mixed $payload, string $deviceType, string $deviceId): void
    {
        $repository = match ($deviceType) {
            self::SENSOR => $this->sensorRepository,
            self::RELAY => $this->relayRepository,
            self::SECURE => $this->securityRepository,
            self::FIRE_SECURE => $this->fireSecurityRepository,

            default => throw SaveDeviceStateException::undefined($deviceType),
        };

        $this->setTransaction($repository, $deviceId, $payload);
    }

    private function setTransaction(BaseDoctrineRepository $repository, string $id, mixed $payload): void
    {
        $this->transaction->transactional(
            function () use ($repository, $id, $payload) {
                $entity = $repository->findById($id);

                if (null !== $entity) {
                    $entity->setPayload($payload);

                    $repository->save($entity);
                }
            }
        );
    }

    private function alert(string $message): void
    {
        $event = new AlertNotificationEvent($message, [
            AlertNotificationEvent::MESSENGER,
        ]);
        $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
    }
}