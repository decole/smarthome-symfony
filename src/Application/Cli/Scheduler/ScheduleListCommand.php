<?php

namespace App\Application\Cli\Scheduler;

use App\Domain\Contract\Repository\ScheduleTaskRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:schedule:list', description: 'List background task')]
final class ScheduleListCommand extends Command
{
    private const CELL_CHARS = 30;

    public function __construct(private readonly ScheduleTaskRepositoryInterface $repository)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $i = 1;
        $taskList = $this->repository->findAllActive();

        $this->printTableRow(['Number', 'Command', 'Interval', 'Last Run', 'Next Run'], $output);
        $this->printEmptyRow($output);

        foreach ($taskList as $task) {
            $data = json_encode($task->getArguments(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $this->printTableRow(
                [
                    $i++,
                    "{$task->getCommand()} {$data}",
                    $task->getInterval(),
                    $task->getLastRun()?->format('d.m.Y H:i:s'),
                    $task->getNextRun()?->format('d.m.Y H:i:s')
                ],
                $output
            );

            $this->printEmptyRow($output);
        }

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
        $template = '------------------------------';
        $output->writeln("{$template}   {$template}   {$template}   {$template}   {$template}");
    }
}