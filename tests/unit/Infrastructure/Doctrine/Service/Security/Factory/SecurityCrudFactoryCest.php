<?php

namespace App\Tests\unit\Infrastructure\Doctrine\Service\Security\Factory;

use App\Application\Service\Validation\FireSecurity\FireSecurityCrudValidationService;
use App\Application\Service\Validation\Security\SecurityCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\SecurityRepositoryInterface;
use App\Infrastructure\Doctrine\Repository\Security\SecurityRepository;
use App\Infrastructure\Doctrine\Service\Security\Factory\SecurityCrudFactory;
use App\Tests\UnitTester;

class SecurityCrudFactoryCest
{
 public function getRepository(UnitTester $I): void
    {
        $service = $this->getService($I);

        $repository = $service->getRepository();

        $I->assertInstanceOf(SecurityRepositoryInterface::class, $repository);
        $I->assertInstanceOf(SecurityRepository::class, $repository);
    }

    public function getValidationService(UnitTester $I): void
    {
        $service = $this->getService($I);

        $validation = $service->getValidationService();

        $I->assertInstanceOf(ValidationInterface::class, $validation);
        $I->assertInstanceOf(SecurityCrudValidationService::class, $validation);
    }

    private function getService(UnitTester $I): SecurityCrudFactory
    {
        return $I->grabService(SecurityCrudFactory::class);
    }
}