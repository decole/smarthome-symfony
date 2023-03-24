<?php

namespace App\Application\Cli\Scheduler;

use App\Domain\ScheduleTask\Input\ScheduleTaskInputDto;
use App\Domain\ScheduleTask\Service\ScheduleTaskService;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'cli:schedule:add', description: 'Add background task')]
final class ScheduleAddCommand extends Command
{
    public function __construct(private readonly ScheduleTaskService $service)
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $questionCommand = new Question("Insert console command:\n", null);
        $questionArguments = new Question(
            "Command have parameters? Empty, or example: argumentOne=parameter argumentTwo=10\n",
            null
        );
        $questionInterval = new Question(
            "Interval, empty if start once, example interval: @hourly, 1 day, 30 minutes, cron format - * * * * *\n",
            null
        );
        $questionNextRun = new Question(
            "Run as date. Example: empty - now or 2023-01-12 00:00:00\n",
            null
        );

        $command = $helper->ask($input, $output, $questionCommand);

        if ($command === null) {
            $output->writeln('Command value is required!');
            return Command::FAILURE;
        }

        $arguments = $helper->ask($input, $output, $questionArguments);
        $interval = $helper->ask($input, $output, $questionInterval);
        $nextRun = $helper->ask($input, $output, $questionNextRun);

        $this->service->add($this->hydrateAnswers($command, $arguments, $interval, $nextRun, $output));

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function hydrateAnswers(
        string $command,
        ?string $rawArguments,
        ?string $rawInterval,
        ?string $rawNextRun,
        OutputInterface $output
    ): ScheduleTaskInputDto {
        $arguments = [];
        $nextRun = new DateTimeImmutable();

        if ($rawArguments !== null && $rawArguments !== '') {
            foreach (explode(' ', $rawArguments) as $list) {
                $map = explode('=', $list);

                $arguments[$map[0]] = $map[1];
            }
        }

        if ($rawNextRun !== null && $rawNextRun !== '') {
            $nextRun = new DateTimeImmutable($rawNextRun);
        }

        return new ScheduleTaskInputDto(
            command: $command,
            arguments: $arguments,
            interval: $this->getInterval($rawInterval, $output),
            nextRun: $nextRun
        );
    }

    private function getInterval(?string $rawInterval, OutputInterface $output): ?string
    {
        if ($rawInterval !== null && $this->service->getNextDate($rawInterval) !== null) {
            return $rawInterval;
        }

        return null;
    }
}