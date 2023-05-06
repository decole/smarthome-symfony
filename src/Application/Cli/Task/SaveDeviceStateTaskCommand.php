<?php

namespace App\Application\Cli\Task;

use App\Domain\Contract\Repository\FireSecurityRepositoryInterface as FireSecurityRepoAlias;
use App\Domain\Contract\Repository\SecurityRepositoryInterface as SecurityRepoAlias;
use App\Domain\Contract\Repository\SensorRepositoryInterface as SensorRepoAlias;
use App\Domain\Contract\Repository\RelayRepositoryInterface as RelayRepoAlias;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\Common\Transactions\TransactionInterface;
use App\Application\Exception\SaveDeviceStateException;
use App\Domain\DeviceData\Service\DeviceCacheService;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Domain\FireSecurity\Entity\FireSecurity;
use Symfony\Component\Console\Command\Command;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Security\Entity\Security;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\Relay\Entity\Relay;
use Throwable;

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
        private readonly SensorRepoAlias $sensorRepository,
        private readonly RelayRepoAlias $relayRepository,
        private readonly SecurityRepoAlias $securityRepository,
        private readonly FireSecurityRepoAlias $fireSecurityRepository,
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

    private function setTransaction(
        SensorRepoAlias|RelayRepoAlias|SecurityRepoAlias|FireSecurityRepoAlias $repository,
        string $id,
        mixed $payload
    ): void {
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