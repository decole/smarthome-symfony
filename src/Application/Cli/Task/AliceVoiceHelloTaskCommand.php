<?php

namespace App\Application\Cli\Task;

use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Необходимость периодически тестировать работоспособность оповещения через колонку
 */
#[AsCommand(name: 'cli:task:alice-morning', description: 'Alice morning hello')]
final class AliceVoiceHelloTaskCommand extends Command
{
    public function __construct(private readonly QuasarNotificationService $service)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->service->send('Доброе утро');

        return Command::SUCCESS;
    }
}