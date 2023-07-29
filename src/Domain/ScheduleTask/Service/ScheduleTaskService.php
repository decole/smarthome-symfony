<?php

declare(strict_types=1);

namespace App\Domain\ScheduleTask\Service;

use App\Domain\Common\Transactions\TransactionInterface;
use App\Domain\Contract\Repository\ScheduleTaskRepositoryInterface;
use App\Domain\ScheduleTask\Entity\ScheduleTask;
use App\Domain\ScheduleTask\Input\ScheduleTaskInputDto;
use Cron\CronExpression;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class ScheduleTaskService
{
    public function __construct(
        private readonly ScheduleTaskRepositoryInterface $repository,
        private readonly TransactionInterface $transaction,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Application $application, OutputInterface $output): void
    {
        $taskList = $this->repository->findAllActive();

        foreach ($taskList as $task) {
            $this->handle($task, $application, $output);
        }
    }

    public function handle(ScheduleTask $task, Application $application, OutputInterface $output): void
    {
        if ($task->getNextRun() === null) {
            $this->logger->info('Catch stab handle command', [
                'command' => $task->getCommand(),
            ]);

            return;
        }

        $nextRunDate = $task->getNextRun()->getTimestamp();
        $currentDate = (new DateTimeImmutable())->getTimestamp();

        if ($currentDate > $nextRunDate) {
            $this->begin($task);

            try {
                $returnCode = $application->find($task->getCommand())
                    ->run(new ArrayInput($task->getArguments()), $output);

                if ($returnCode === Command::SUCCESS) {
                    $this->end($task);
                }
            } catch (Throwable $exception) {
                $this->logger->critical('Crash handling command', [
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }

    public function add(ScheduleTaskInputDto $dto): ScheduleTask
    {
        $task = new ScheduleTask(
            command: $dto->command,
            arguments: $dto->arguments,
            interval: $dto->interval,
            lastRun: null,
            nextRun: $dto->nextRun
        );

        $this->save($task);

        return $task;
    }

    public function delete(ScheduleTask $task): void
    {
        $this->transaction->transactional(
            fn () => $this->repository->delete($task)
        );
    }

    public function save(ScheduleTask $task): void
    {
        $this->transaction->transactional(
            fn() => $this->repository->save($task)
        );
    }

    public function getNextDate(?string $interval): ?DateTimeImmutable
    {
        if ($interval === null || $interval === '') {
            return null;
        }

        $date = match (str_contains($interval, '*') ||
            str_contains($interval, '@') ||
            str_contains($interval, '/')
        ) {
            true => DateTimeImmutable::createFromMutable((new CronExpression($interval))->getNextRunDate()),
            default => (new DateTimeImmutable())->modify($interval),
        };

        if ($date === false) {
            return null;
        }

        return $date;
    }

    private function begin(ScheduleTask $task): void
    {
        $task->setNextRun(null);

        $this->save($task);
    }

    private function end(ScheduleTask $task): void
    {
        $task->setLastRun();

        $interval = $task->getInterval();

        $date = match ($interval) {
            null, '' => null,
            default => $this->getNextDate($interval),
        };

        $task->setNextRun($date);

        $this->save($task);
    }
}