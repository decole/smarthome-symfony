<?php

namespace App\Application\Cli;

use App\Domain\PLC\Service\PlcHandleService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:plc')]
final class MqttPlcHandlerCommand extends Command
{
    public function __construct(
        private readonly PlcHandleService $handler
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Мониторит контроллеры на рабочее состояник');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->handler->execute();

        return Command::SUCCESS;
    }
}