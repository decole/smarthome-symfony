<?php

namespace App\Application\Cli;

use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class QuasarDeviceListCommand extends Command
{
    protected static $defaultName = 'cli:quasar:device-list';

    public function __construct(private QuasarNotificationService $service)
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
            dump($device->getId() . ' ' . $device->getName() . ' ' . $device->getType());
        }

        return 0;
    }
}