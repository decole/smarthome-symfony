<?php

namespace App\Application\Cli;

use App\Infrastructure\Mqtt\Service\MqttHandleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MqttHandlerCommand extends Command
{
    protected static $defaultName = 'cli:mqtt';

    public function __construct(
        private readonly MqttHandleService $handler,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Слушает MQTT транспорт');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->handler->listen();

        return 0;
    }
}