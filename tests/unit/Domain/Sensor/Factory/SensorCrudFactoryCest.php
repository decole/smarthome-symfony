<?php

namespace App\Tests\unit\Domain\Sensor\Factory;

use App\Application\Service\Validation\Sensor\SensorCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\SensorRepositoryInterface;
use App\Domain\Sensor\Factory\SensorCrudFactory;
use App\Infrastructure\Doctrine\Repository\Sensor\SensorRepository;
use App\Tests\UnitTester;

class SensorCrudFactoryCest
{
    public function getRepository(UnitTester $I): void
    {
        $service = $this->getService($I);

        $repository = $service->getRepository();

        $I->assertInstanceOf(SensorRepositoryInterface::class, $repository);
        $I->assertInstanceOf(SensorRepository::class, $repository);
    }

    public function getValidationService(UnitTester $I): void
    {
        $service = $this->getService($I);

        $validation = $service->getValidationService();

        $I->assertInstanceOf(ValidationInterface::class, $validation);
        $I->assertInstanceOf(SensorCrudValidationService::class, $validation);
    }

    private function getService(UnitTester $I): SensorCrudFactory
    {
        return $I->grabService(SensorCrudFactory::class);
    }
}