<?php

namespace App\Application\Cli;

use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli:quasar:delete-scenario')]
final class QuasarDeleteScenarioCommand extends Command
{
    public function __construct(private readonly QuasarNotificationService $service)
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Удаление сценария для оповещения в умном доме Яндекс');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Delete scenario by Alice smart Home (Yandex)</info>');
        $output->writeln($this->service->deleteScenario());

        return Command::SUCCESS;
    }
}