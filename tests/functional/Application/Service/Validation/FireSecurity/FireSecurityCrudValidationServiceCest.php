<?php

namespace App\Tests\functional\Application\Service\Validation\FireSecurity;

use App\Application\Http\Web\FireSecurity\Dto\CrudFireSecurityDto;
use App\Application\Service\Validation\FireSecurity\FireSecurityCrudValidationService;
use App\Domain\FireSecurity\Service\FireSecurityCrudService;
use App\Tests\FunctionalTester;
use Symfony\Component\Validator\ConstraintViolationList;

class FireSecurityCrudValidationServiceCest
{
    public function positiveValidateCreate(FunctionalTester $I): void
    {
        $dto = new CrudFireSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->normalPayload = $I->faker()->word();
        $dto->alertPayload = $I->faker()->word();
        $dto->lastCommand = $I->faker()->word();
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

    public function positiveValidateUpdate(FunctionalTester $I): void
    {
        $dto = new CrudFireSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->normalPayload = $I->faker()->word();
        $dto->alertPayload = $I->faker()->word();
        $dto->lastCommand = $I->faker()->word();
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

    public function negativeValidateCreate(FunctionalTester $I): void
    {
        $dto = new CrudFireSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->normalPayload = $I->faker()->word();
        $dto->alertPayload = $I->faker()->word();
        $dto->lastCommand = $I->faker()->word();
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
        $I->assertEquals('Fire security device name already exist.', $result[0]->getMessage());
        $I->assertEquals('Fire security device topic already exist.', $result[1]->getMessage());
    }

    public function positiveValidateUpdateExistEntity(FunctionalTester $I): void
    {
        $dto = new CrudFireSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->normalPayload = $I->faker()->word();
        $dto->alertPayload = $I->faker()->word();
        $dto->lastCommand = $I->faker()->word();
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

    private function getService(FunctionalTester $I): FireSecurityCrudValidationService
    {
        return $I->grabService(FireSecurityCrudValidationService::class);
    }

    private function crudService(FunctionalTester $I): FireSecurityCrudService
    {
        return $I->grabService(FireSecurityCrudService::class);
    }
}