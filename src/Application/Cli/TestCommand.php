<?php

namespace App\Application\Cli;

use App\Application\Service\DeviceData\DataCacheService;
use App\Domain\Doctrine\Identity\Repository\UserRepositoryInterface;
use App\Domain\Notification\Event\NotificationEvent;
use App\Domain\Notification\TelegramNotification;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private DataCacheService $dataCacheService,
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
//        $notify = new TelegramNotification(1198443517, 'test');
//        $event = new NotificationEvent($notify);
//        $this->eventDispatcher->dispatch($event, NotificationEvent::NAME);

        // php bin/console messenger:consume async -vv

//        $res = $this->dataCacheService->getList();
//        dump($res);
        $users = $this->userRepository->findAllWithTelegramId();

        dump(count($users));

        return 0;
    }
}