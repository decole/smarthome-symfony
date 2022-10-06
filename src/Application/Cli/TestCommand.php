<?php

namespace App\Application\Cli;

use App\Application\Service\VisualNotification\Dto\VisualNotificationDto;
use App\Application\Service\VisualNotification\VisualNotificationService;
use App\Domain\Doctrine\VisualNotification\Entity\VisualNotification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private VisualNotificationService $service
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // создание визуального уведомления
        $dto = new VisualNotificationDto(VisualNotification::ALERT_TYPE, 'test message');
        $this->service->save($dto);

        sleep(1);

        // статус прочитанно
        $this->service->setIsRead();

        return 0;
    }
}