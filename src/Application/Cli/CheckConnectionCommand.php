<?php

declare(strict_types=1);

namespace App\Application\Cli;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'deploy:check-connection')]
final class CheckConnectionCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->entityManager->createNativeQuery('SELECT 1', new ResultSetMapping())->execute();
        } catch (\Throwable $exception) {
            $output->writeln('<error>Database connection failed.</error>');

            return Command::FAILURE;
        }

        if ($this->entityManager->getConnection()->isConnected()) {
            $output->writeln('<info>Database connection successful!</info>');

            return Command::SUCCESS;
        }

        $output->writeln('<error>Database connection failed.</error>');

        return Command::FAILURE;
    }
}
