<?php

namespace App\Application\Cli\Scheduler;

use App\Domain\Contract\Repository\ScheduleTaskRepositoryInterface;
use App\Domain\ScheduleTask\Service\ScheduleTaskService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'cli:schedule:delete', description: 'Delete background task')]
final class ScheduleDeleteCommand extends Command
{
    private const CELL_CHARS = 20;

    public function __construct(
        private readonly ScheduleTaskService $service,
        private readonly ScheduleTaskRepositoryInterface $repository
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $questionCommand = new Question("Insert deleting task number :\n", null);
        $map = [];
        $i = 1;
        $list = $this->repository->findAll();

        if ($list === []) {
            $output->writeln('Not have tasks. Count = 0.');

            return Command::SUCCESS;
        }

        $this->printTableRow(['Number', 'Command', 'Interval', 'Last Run', 'Next Run'], $output);
        $this->printEmptyRow($output);

        foreach ($list as $key => $task) {
            $map[$i] = $key;

            $this->printTableRow(
                [
                    $i++,
                    $task->getCommand(),
                    $task->getInterval(),
                    $task->getLastRun()?->format('d.m.Y H:i:s'),
                    $task->getNextRun()?->format('d.m.Y H:i:s')
                ],
                $output
            );

            $this->printEmptyRow($output);
        }

        $number = $helper->ask($input, $output, $questionCommand);

        if ($number === null) {
            $output->writeln('Number task is required!');

            return Command::FAILURE;
        }

        if ((int)$number === 0 || (int)$number > count($list)) {
            $output->writeln('Number out of range count task!');

            return Command::FAILURE;
        }

        $this->service->delete($list[$map[$number]]);

        return Command::SUCCESS;
    }

    private function printTableRow(array $list, OutputInterface $output): void
    {
        $lastIndex = count($list) - 1;
        $nextRow = [];
        $printNextRow = false;

        foreach ($list as $key => $val) {
            $len = strlen($val);
            $formattedVal = '';

            if ($len === self::CELL_CHARS) {
                $formattedVal = $val;
                $nextRow[] = '';
            } elseif ($len > self::CELL_CHARS) {
                $formattedVal = substr($val, 0, self::CELL_CHARS);
                $nextRow[] = substr($val, self::CELL_CHARS);
                $printNextRow = true;
            } elseif ($len < self::CELL_CHARS) {
                $formattedVal = str_pad($val, self::CELL_CHARS, ' ', STR_PAD_BOTH);
                $nextRow[] = '';
            }

            $output->write($formattedVal);

            if ($key !== $lastIndex) {
                $output->write(' | ');
            }
        }

        $output->writeln('');

        if ($printNextRow) {
            $this->printTableRow($nextRow, $output);
        }
    }

    private function printEmptyRow(OutputInterface $output): void
    {
        $template = '--------------------';
        $output->writeln("{$template}   {$template}   {$template}   {$template}   {$template}");
    }
}