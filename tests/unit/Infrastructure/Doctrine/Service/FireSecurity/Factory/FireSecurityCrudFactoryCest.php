<?php

namespace App\Tests\unit\Infrastructure\Doctrine\Service\FireSecurity\Factory;

use App\Application\Service\Validation\FireSecurity\FireSecurityCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\FireSecurityRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\FireSecurity\FireSecurityRepository;
use App\Infrastructure\Doctrine\Service\FireSecurity\Factory\FireSecurityCrudFactory;
use App\Tests\UnitTester;

class FireSecurityCrudFactoryCest
{
    public function getRepository(UnitTester $I): void
    {
        $service = $this->getService($I);

        $repository = $service->getRepository();

        $I->assertInstanceOf(FireSecurityRepositoryInterface::class, $repository);
        $I->assertInstanceOf(FireSecurityRepository::class, $repository);
    }

    public function getValidationService(UnitTester $I): void
    {
        $service = $this->getService($I);

        $validation = $service->getValidationService();

        $I->assertInstanceOf(ValidationInterface::class, $validation);
        $I->assertInstanceOf(FireSecurityCrudValidationService::class, $validation);
    }

    private function getService(UnitTester $I): FireSecurityCrudFactory
    {
        return $I->grabService(FireSecurityCrudFactory::class);
    }
}