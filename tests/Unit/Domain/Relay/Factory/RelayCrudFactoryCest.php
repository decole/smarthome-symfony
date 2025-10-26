<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Relay\Factory;

use App\Application\Service\Validation\Relay\RelayCrudValidationService;
use App\Domain\Contract\CrudValidation\ValidationInterface;
use App\Domain\Contract\Repository\RelayRepositoryInterface;
use App\Domain\Relay\Factory\RelayCrudFactory;
use App\Infrastructure\Repository\Relay\RelayRepository;
use App\Tests\UnitTester;
use Codeception\Attribute\Skip;

#[Skip('This test not support new version codeception')]
class RelayCrudFactoryCest
{
    public function getRepository(UnitTester $I): void
    {
        $service = $this->getService($I);

        $repository = $service->getRepository();

        $I->assertInstanceOf(RelayRepositoryInterface::class, $repository);
        $I->assertInstanceOf(RelayRepository::class, $repository);
    }

    public function getValidationService(UnitTester $I): void
    {
        $service = $this->getService($I);

        $validation = $service->getValidationService();

        $I->assertInstanceOf(ValidationInterface::class, $validation);
        $I->assertInstanceOf(RelayCrudValidationService::class, $validation);
    }

    private function getService(UnitTester $I): RelayCrudFactory
    {
        return $I->grabService(RelayCrudFactory::class);
    }
}
