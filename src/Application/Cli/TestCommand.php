<?php

namespace App\Application\Cli;

use App\Domain\Contract\Repository\PageRepositoryInterface;
use App\Domain\Doctrine\FireSecurity\Entity\FireSecurity;
use App\Domain\Doctrine\Page\Entity\Page;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'cli:test';

    public function __construct(
        private PageRepositoryInterface $pageRepository
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = [
            Sensor::alias() => [
//                '4f6361cb-734f-499c-9954-da4eac7faf97',
//                '9f2867b2-4333-4eae-9b32-74a425f83203',
//                'b60b59e9-e368-46d3-a55a-e524183e5cba',
//                'f374b325-c036-4151-a9c9-087cee71ec74',
//                'fcc2e04e-e729-45c7-a9f0-23967075910d',
//                '04f16adf-82db-46c8-8753-040f21a348a0',
//                '3d633047-66f0-44b7-8bca-aea57a5b40bc',
//                '714b4c18-51cb-47f4-a934-0fd97c33dcaf',
//                '2717da14-0882-4fcc-9890-556ed1bff122',
//                '370911b8-8ecf-4641-8a32-9aef33f7f3a1',
            ],
            Relay::alias() => [
//                '3a08b02f-c3e1-43ed-835c-225b47f14587',
                '2e084e77-0e40-454c-81d1-d98513cdf183',
            ],
            Security::alias() => [],
            FireSecurity::alias() => []
        ];

        $page = new Page(
            name: 'watering',
            config: $config
        );
        $this->pageRepository->save($page);

        return 0;
    }
}
