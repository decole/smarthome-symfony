<?php

declare(strict_types=1);

namespace App\Tests\Functional\Application\Service\Validation\Sensor;

use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Application\Service\Validation\Sensor\SensorCrudValidationService;
use App\Domain\Sensor\Service\SensorCrudService;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\Examples;
use Codeception\Example;
use Symfony\Component\Validator\ConstraintViolationList;

class SensorCrudValidationServiceCest
{
    #[Examples('temperature')]
    #[Examples('humidity')]
    #[Examples('leakage')]
    #[Examples('pressure')]
    #[Examples('dryContact')]
    public function positiveValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->payloadMin = '0';
        $dto->payloadMax = '100';
        $dto->payloadDry = $dto->payloadLow = '0';
        $dto->payloadWet = $dto->payloadHigh = '1';
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $service = $this->getService($I);

        $service->setValue($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(false);

        $I->assertEquals(0, $result->count());
    }

    #[Examples('temperature')]
    #[Examples('humidity')]
    #[Examples('leakage')]
    #[Examples('pressure')]
    #[Examples('dryContact')]
    public function positiveValidateUpdate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->payloadMin = '0';
        $dto->payloadMax = '100';
        $dto->payloadDry = $dto->payloadLow = '0';
        $dto->payloadWet = $dto->payloadHigh = '1';
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $service = $this->getService($I);

        $service->setValue($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    #[Examples('temperature')]
    #[Examples('humidity')]
    #[Examples('leakage')]
    #[Examples('pressure')]
    #[Examples('dryContact')]
    public function negativeValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->payloadMin = '0';
        $dto->payloadMax = '100';
        $dto->payloadDry = $dto->payloadLow = '0';
        $dto->payloadWet = $dto->payloadHigh = '1';
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $service = $this->getService($I);

        $service->setValue($dto);

        $this->crudService($I)->create($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(false);

        $I->assertEquals(2, $result->count());
        $I->assertEquals('Sensor name already exist.', $result[0]->getMessage());
        $I->assertEquals('Sensor topic already exist.', $result[1]->getMessage());
    }

    #[Examples('temperature')]
    #[Examples('humidity')]
    #[Examples('leakage')]
    #[Examples('pressure')]
    #[Examples('dryContact')]
    public function positiveValidateUpdateExistEntity(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->payloadMin = '0';
        $dto->payloadMax = '100';
        $dto->payloadDry = $dto->payloadLow = '0';
        $dto->payloadWet = $dto->payloadHigh = '1';
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $service = $this->getService($I);

        $service->setValue($dto);

        $this->crudService($I)->create($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    private function getService(FunctionalTester $I): SensorCrudValidationService
    {
        return $I->grabService(SensorCrudValidationService::class);
    }

    private function crudService(FunctionalTester $I): SensorCrudService
    {
        return $I->grabService(SensorCrudService::class);
    }
}
