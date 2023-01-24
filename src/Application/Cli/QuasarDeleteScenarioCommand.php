<?php

namespace App\Application\Cli;

use App\Infrastructure\Quasar\Service\QuasarNotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class QuasarDeleteScenarioCommand extends Command
{
    protected static $defaultName = 'cli:quasar:delete-scenario';

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
        dump('Delete scenario by Alice smart Home (Yandex)');

        dump($this->service->deleteScenario());

        return 0;
    }
}