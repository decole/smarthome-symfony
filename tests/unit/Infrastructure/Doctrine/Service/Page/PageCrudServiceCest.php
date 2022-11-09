<?php

namespace App\Tests\unit\Infrastructure\Doctrine\Service\Page;

use App\Infrastructure\Doctrine\Service\Page\PageCrudService;
use App\Tests\UnitTester;
use Symfony\Component\HttpFoundation\Request;

class PageCrudServiceCest
{
    public function createEmptyPageDto(UnitTester $I): void
    {
        $service = $this->getService($I);

        $dto = $service->createDto(null);

        $I->assertEquals('new page', $dto->name);
        $I->assertEquals($this->getDefaultConfig(), $dto->config);
    }

    public function createByRequestPageDto(UnitTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'name' => $name = $I->faker()->word,
            'config' => $this->getDefaultConfig(),
        ]);

        $dto = $service->createDto($request);

        $I->assertEquals($name, $dto->name);
        $I->assertEquals($this->getDefaultConfig(), $dto->config);
    }

    public function createByNullRequestPageDto(UnitTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'name' => null,
            'config' => null,
        ]);

        $dto = $service->createDto($request);

        $I->assertEquals('new page', $dto->name);
        $I->assertEquals($this->getDefaultConfig(), $dto->config);
    }

    private function getService(UnitTester $I): PageCrudService
    {
        return $I->grabService(PageCrudService::class);
    }

    private function getDefaultConfig(): array
    {
        return [
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
    }
}