<?php

namespace App\Application\Cli;

use App\Domain\PLC\Service\PlcHandleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MqttPlcHandlerCommand extends Command
{
    private const DELAY = 60;
    protected static $defaultName = 'cli:plc';

    public function __construct(
        private PlcHandleService $handler
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

        return 0;
    }
}