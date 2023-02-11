<?php

namespace App\Application\Cli;

use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use App\Application\Service\PeriodicHandle\CriteriaChainService;
use App\Domain\Event\AlertNotificationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class PeriodicHandlerCommand extends Command
{
    protected static $defaultName = 'cli:cron';

    public function __construct(
        private readonly CriteriaChainService $service,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Команда выполняющая периодичные действия');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->schedule();
        } catch (Throwable $exception) {
            $this->logger->warning('crash periodic handler', [
                'exception' => $exception->getMessage(),
            ]);

            $message = 'Сервис периодических заданий сломался!';

            $event = new AlertNotificationEvent($message, [
                AlertNotificationEvent::MESSENGER,
                AlertNotificationEvent::DISCORD,
                AlertNotificationEvent::ALICE
            ]);
            $this->eventDispatcher->dispatch($event, AlertNotificationEvent::NAME);
        }

        return Command::SUCCESS;
    }

    private function schedule(): void
    {
        $map = $this->service->getCriteriaMap();

        /** @var PeriodicHandleCriteriaInterface $criteria */
        foreach ($map as $criteria) {
            if ($criteria->isDue()) {
                $criteria->execute();
            }
        }
    }
}