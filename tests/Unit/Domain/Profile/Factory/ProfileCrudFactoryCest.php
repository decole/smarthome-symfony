<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Profile\Factory;

use App\Application\Service\Validation\Profile\ProfileCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\ProfileRepositoryInterface;
use App\Domain\Profile\Factory\ProfileCrudFactory;
use App\Infrastructure\Repository\Profile\ProfileRepository;
use App\Tests\UnitTester;
use Codeception\Attribute\Skip;

#[Skip('This test not support new version codeception')]
class ProfileCrudFactoryCest
{
    public function getRepository(UnitTester $I): void
    {
        $service = $this->getService($I);

        $repository = $service->getRepository();

        $I->assertInstanceOf(ProfileRepositoryInterface::class, $repository);
        $I->assertInstanceOf(ProfileRepository::class, $repository);
    }

    public function getValidationService(UnitTester $I): void
    {
        $service = $this->getService($I);

        $validation = $service->getValidationService();

        $I->assertInstanceOf(ValidationInterface::class, $validation);
        $I->assertInstanceOf(ProfileCrudValidationService::class, $validation);
    }

    private function getService(UnitTester $I): ProfileCrudFactory
    {
        return $I->grabService(ProfileCrudFactory::class);
    }
}
