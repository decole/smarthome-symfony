<?php

namespace App\Application\Cli;

use App\Domain\Notification\Event\NotificationEvent;
use App\Domain\Notification\TelegramNotification;
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
        $notify = new TelegramNotification(1198443517, 'test');
        $event = new NotificationEvent($notify);
        $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);

        // php bin/console messenger:consume async -vv

        return 0;
    }
}