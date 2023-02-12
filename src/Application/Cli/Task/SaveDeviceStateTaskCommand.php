<?php

namespace App\Application\Cli\Task;

use App\Application\Exception\SaveDeviceStateException;
use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Security\Entity\Security;
use App\Domain\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Repository\BaseDoctrineRepository;
use Psr\Cache\InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Периодически сохраняет данные датчиков в базу данных.
 */
#[AsCommand(name: 'cli:task:save-device-state', description: 'Periodic save device state in DB')]
class SaveDeviceStateTaskCommand extends Command
{
    private const RELAY = 'r';
    private const SENSOR = 's';
    private const SECURE = 'se';
    private const FIRE_SECURE = 'f';

    public function __construct(
        private readonly DeviceDataCacheService $service,
        private readonly DeviceCacheService $cacheService,
        private readonly SensorRepositoryInterface $sensorRepository,
        private readonly RelayRepositoryInterface $relayRepository,
        private readonly SecurityRepositoryInterface $securityRepository,
        private readonly FireSecurityRepositoryInterface $fireSecurityRepository,
        private readonly TransactionInterface $transaction,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->handle();
        } catch (Throwable $exception) {
            $this->alert($exception->getMessage());
        }

        return Command::SUCCESS;
    }

    /**
     * @return void
     * @throws SaveDeviceStateException|InvalidArgumentException
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