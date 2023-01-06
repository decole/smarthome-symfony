<?php

namespace App\Tests\unit\Domain\Page\Factory;

use App\Application\Service\Validation\Page\PageCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\PageRepositoryInterface;
use App\Domain\Page\Factory\PageCrudFactory;
use App\Infrastructure\Doctrine\Repository\Page\PageRepository;
use App\Tests\UnitTester;

class PageCrudFactoryCest
{
 public function getRepository(UnitTester $I): void
    {
        $service = $this->getService($I);

        $repository = $service->getRepository();

        $I->assertInstanceOf(PageRepositoryInterface::class, $repository);
        $I->assertInstanceOf(PageRepository::class, $repository);
    }

    public function getValidationService(UnitTester $I): void
    {
        $service = $this->getService($I);

        $validation = $service->getValidationService();

        $I->assertInstanceOf(ValidationInterface::class, $validation);
        $I->assertInstanceOf(PageCrudValidationService::class, $validation);
    }

    private function getService(UnitTester $I): PageCrudFactory
    {
        return $I->grabService(PageCrudFactory::class);
    }
}