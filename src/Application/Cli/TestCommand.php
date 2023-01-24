<?php

namespace App\Application\Cli;

use App\Application\Service\Factory\DeviceAlertFactory;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Domain\Payload\Entity\DevicePayload;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private readonly SecurityRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $device = $this->repository->findById('da441d4f-fa6b-4d79-b8cd-1bbead904ba6');

        if ($device === null) {
            return 1;
        }

        $payload = new DevicePayload(
            topic: $device->getTopic(),
            payload: $device->getHoldPayload()
        );

        $factory = new DeviceAlertFactory($this->eventDispatcher);

        $validator = $factory->create(
            device: $device,
            payload: $payload
        );

        dd($validator->notify());

        return 0;
    }
}