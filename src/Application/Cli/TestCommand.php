<?php

namespace App\Application\Cli;

use App\Domain\Event\NotificationEvent;
use App\Domain\Notification\Entity\TelegramNotificationMessage;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = new NotificationEvent(new TelegramNotificationMessage('test message bi cli:test', 1198443517));
        $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);

        return 0;
    }
}