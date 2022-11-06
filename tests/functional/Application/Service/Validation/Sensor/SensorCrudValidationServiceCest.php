<?php

namespace App\Tests\functional\Application\Service\Validation\Sensor;

use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Application\Service\Validation\Sensor\SensorCrudValidationService;
use App\Infrastructure\Doctrine\Service\Sensor\SensorCrudService;
use App\Tests\FunctionalTester;
use Codeception\Example;
use Symfony\Component\Validator\ConstraintViolationList;

class SensorCrudValidationServiceCest
{
    /**
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     *
     * @param FunctionalTester $I
     * @param Example $example
     * @return void
     */
    public function positiveValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $I->faker()->word;
        $dto->topic = $I->faker()->word;
        $dto->payload = $I->faker()->word;
        $dto->payload_min = 0;
        $dto->payload_max = 100;
        $dto->payload_dry = $dto->payload_low = 0;
        $dto->payload_wet = $dto->payload_high = 1;
        $dto->message_info = $I->faker()->word;
        $dto->message_ok = $I->faker()->word;
        $dto->message_warn = $I->faker()->word;
        $dto->status = 'on';
        $dto->notify = 'on';

        $service = $this->getService($I);

        $service->setValue($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(false);

        $I->assertEquals(0, $result->count());
    }

    /**
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     *
     * @param FunctionalTester $I
     * @param Example $example
     * @return void
     */
    public function positiveValidateUpdate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $I->faker()->word;
        $dto->topic = $I->faker()->word;
        $dto->payload = $I->faker()->word;
        $dto->payload_min = 0;
        $dto->payload_max = 100;
        $dto->payload_dry = $dto->payload_low = 0;
        $dto->payload_wet = $dto->payload_high = 1;
        $dto->message_info = $I->faker()->word;
        $dto->message_ok = $I->faker()->word;
        $dto->message_warn = $I->faker()->word;
        $dto->status = 'on';
        $dto->notify = 'on';

        $service = $this->getService($I);

        $service->setValue($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    /**
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     *
     * @param FunctionalTester $I
     * @param Example $example
     * @return void
     */
    public function negativeValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $I->faker()->word;
        $dto->topic = $I->faker()->word;
        $dto->payload = $I->faker()->word;
        $dto->payload_min = 0;
        $dto->payload_max = 100;
        $dto->payload_dry = $dto->payload_low = 0;
        $dto->payload_wet = $dto->payload_high = 1;
        $dto->message_info = $I->faker()->word;
        $dto->message_ok = $I->faker()->word;
        $dto->message_warn = $I->faker()->word;
        $dto->status = 'on';
        $dto->notify = 'on';

        $service = $this->getService($I);

        $service->setValue($dto);

        $s = $this->crudService($I)->create($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(false);

        $I->assertEquals(2, $result->count());
        $I->assertEquals('Sensor name already exist.', $result[0]->getMessage());
        $I->assertEquals('Sensor topic already exist.', $result[1]->getMessage());
    }

    /**
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     *
     * @param FunctionalTester $I
     * @param Example $example
     * @return void
     */
    public function positiveValidateUpdateExistEntity(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $I->faker()->word;
        $dto->topic = $I->faker()->word;
        $dto->payload = $I->faker()->word;
        $dto->payload_min = 0;
        $dto->payload_max = 100;
        $dto->payload_dry = $dto->payload_low = 0;
        $dto->payload_wet = $dto->payload_high = 1;
        $dto->message_info = $I->faker()->word;
        $dto->message_ok = $I->faker()->word;
        $dto->message_warn = $I->faker()->word;
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