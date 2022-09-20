<?php

namespace App\Application\Cli;

use App\Domain\Notification\AliceNotificationMessage;
use App\Domain\Notification\DiscordNotificationMessage;
use App\Domain\Notification\Event\NotificationEvent;
use App\Infrastructure\Mqtt\Service\MqttHandleService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MqttHandlerCommand extends Command
{
    protected static $defaultName = 'cli:mqtt';

    public function __construct(
        private LoggerInterface $logger,
        private MqttHandleService $handler,
        private EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Слушает MQTT транспорт');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->handler->listen();
        } catch (\Throwable $exception) {
            $this->logger->critical('Crash MQTT listener', [
                'exception' => $exception->getMessage(),
            ]);

            $message = 'Не возможно соединиться с брокером сообщений';

            $event = new NotificationEvent(new AliceNotificationMessage($message));
            $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);

            $event = new NotificationEvent(new DiscordNotificationMessage($message));
            $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);
        }

        return 0;
    }
}