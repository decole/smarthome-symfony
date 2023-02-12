<?php

namespace App\Application\Cli\Task;

use App\Domain\DeviceData\Service\DeviceCacheService;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Relay\Entity\Relay;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Оповестить когда центральный клапан открыт
 * нужно для вторичного мониторинга, чтобы не допустить ошибочной траты ресурсов воды
 * оповестить через алису, телеграм и дискорд
 */
#[AsCommand(name: 'cli:task:watering-on-checker', description: 'Alert by major watering switch is open')]
final class WateringOnAlarmTaskCommand extends Command
{
    private const TOPIC = 'water/check/major';
    private const SWITCH_ON = 1;

    public function __construct(
        private readonly DeviceDataCacheService $service,
        private readonly DeviceCacheService $cacheService,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $payloadCheckOn = self::SWITCH_ON;

        $devices = $this->cacheService->getTopicMapByDeviceTopic();

        foreach ($devices as $device) {
            if ($device instanceof Relay && $device->getCheckTopic() === self::TOPIC) {
                $payloadCheckOn = $device->getCheckTopicPayloadOn();
            }
        }

        $payloadMap = $this->service->getPayloadByTopicList([self::TOPIC]);

        if ($payloadMap[self::TOPIC] === $payloadCheckOn) {
            $this->notification();
        }

        return Command::SUCCESS;
    }

    private function notification(): void
    {
        $message = 'Главный клапан автополива включен';

        $event = new AlertNotificationEvent($message, [
            AlertNotificationEvent::MESSENGER,
            AlertNotificationEvent::ALICE
        ]);

        $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
    }
}