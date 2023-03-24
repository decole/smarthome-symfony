<?php

namespace App\Application\Cli\Scheduler;

use App\Domain\ScheduleTask\Service\ScheduleTaskService;
use PHPUnit\Util\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:schedule:run', description: 'Run background tasks')]
final class SchedulerCommand extends Command
{
    public function __construct(
        private readonly ScheduleTaskService $service
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();

        if ($application === null) {
            throw new Exception('not create application');
        }

        $this->service->execute($application, $output);

        return Command::SUCCESS;
    }
}