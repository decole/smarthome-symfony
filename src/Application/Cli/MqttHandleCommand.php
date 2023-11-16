<?php

namespace App\Application\Cli;

use App\Infrastructure\Mqtt\Service\MqttSubscribeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:mqtt')]
final class MqttHandleCommand extends Command
{
    public function __construct(private readonly MqttSubscribeService $subscribeService)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->subscribeService->execute();

        return Command::SUCCESS;
    }
}