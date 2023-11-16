<?php

namespace App\Application\Cli;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'deploy:check-connection')]
final class CheckConnectionCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->manager->getConnection();

        try {
            $connection->connect();
        } catch (Throwable) {
            $output->write('error');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}