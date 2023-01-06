<?php

namespace App\Tests\functional\Application\Service\Validation\Security;

use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Application\Service\Validation\Security\SecurityCrudValidationService;
use App\Domain\Security\Service\SecurityCrudService;
use App\Tests\FunctionalTester;
use Symfony\Component\Validator\ConstraintViolationList;

class SecurityCrudValidationServiceCest
{
    public function positiveValidateCreate(FunctionalTester $I): void
    {
        $dto = new CrudSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->detectPayload = $I->faker()->word();
        $dto->holdPayload = $I->faker()->word();
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
        $dto = new CrudSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->detectPayload = $I->faker()->word();
        $dto->holdPayload = $I->faker()->word();
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
        $dto = new CrudSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->detectPayload = $I->faker()->word();
        $dto->holdPayload = $I->faker()->word();
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
        $I->assertEquals('Security device name already exist.', $result[0]->getMessage());
        $I->assertEquals('Security device topic already exist.', $result[1]->getMessage());
    }

    public function positiveValidateUpdateExistEntity(FunctionalTester $I): void
    {
        $dto = new CrudSecurityDto();

        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->detectPayload = $I->faker()->word();
        $dto->holdPayload = $I->faker()->word();
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

    private function getService(FunctionalTester $I): SecurityCrudValidationService
    {
        return $I->grabService(SecurityCrudValidationService::class);
    }

    private function crudService(FunctionalTester $I): SecurityCrudService
    {
        return $I->grabService(SecurityCrudService::class);
    }
}