<?php

namespace App\Application\Cli;

use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:quasar:device-list')]
final class QuasarDeviceListCommand extends Command
{
    public function __construct(private readonly QuasarNotificationService $service)
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Список устройств в умном доме Яндекс');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $map = $this->service->getDevices();

        foreach ($map as $device) {
            $output->writeln(sprintf('<info>%s %s %s</info>', $device->getId(), $device->getName(), $device->getType()));
        }

        return Command::SUCCESS;
    }
}