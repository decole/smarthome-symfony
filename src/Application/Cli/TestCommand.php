<?php

namespace App\Application\Cli;

use App\Domain\DeviceData\Service\SecureDeviceDataService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private SecureDeviceDataService $service,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $topic = 'secure/PIR01';
        $result = $this->service->getDeviceState($topic);
        dump('state: ' . (int)$result->standardisedState . ' | isGuard: ' . (int)$result->isGuarded);

//        // the following code will test if monolog integration logs to sentry
//        $this->logger->error('My custom logged error.');
//
//        // the following code will test if an uncaught exception logs to sentry
//        throw new \RuntimeException('Example exception.');

        return 0;
    }
}