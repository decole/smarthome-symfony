<?php

namespace App\Application\Cli;

use App\Domain\Notification\AliceNotificationMessage;
use App\Domain\Notification\DiscordNotificationMessage;
use App\Domain\Notification\Event\NotificationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = 'тест сообщение';
//        $event = new NotificationEvent(new AliceNotificationMessage($message));
        $event = new NotificationEvent(new DiscordNotificationMessage($message));
        $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);

        return 0;
    }
}