<?php

declare(strict_types=1);

namespace App\Application\Cli;

use App\Domain\Event\AlertNotificationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:test')]
class TestCommand extends Command
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('use for developing and debug');

        $text = 'test message by cli test command';

        $event = new AlertNotificationEvent($text, [AlertNotificationEvent::MESSENGER]);
        $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);

        return Command::SUCCESS;
    }
}
