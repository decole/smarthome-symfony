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
        private SecureDeviceDataService $service
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $topic = 'secure/PIR01';
        $result = $this->service->getDeviceState($topic);
        dump('state: ' . (int)$result->standardisedState . ' | isGuard: ' . (int)$result->isGuarded);

        return 0;
    }
}