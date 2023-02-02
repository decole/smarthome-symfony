<?php

namespace App\Application\Cli;

use App\Domain\DeviceData\Service\SecureDeviceDataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private readonly SecureDeviceDataService $service
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $topic= 'hallway/check/door2';

        $this->service->setTrigger($topic, true);

        $t = $this->service->getDeviceState($topic);

        dd($t);

        return 0;
    }
}