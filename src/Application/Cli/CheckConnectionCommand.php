<?php

namespace App\Application\Cli;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckConnectionCommand extends Command
{
    protected static $defaultName = 'deploy:check-connection';

    public function __construct(private EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->manager->getConnection();

        try {
            $connection->connect();
        } catch (\Throwable) {
            $output->write('error');

            return 1;
        }

        return 0;
    }
}