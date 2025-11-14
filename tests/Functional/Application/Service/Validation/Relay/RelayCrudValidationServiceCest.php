<?php

declare(strict_types=1);

namespace App\Tests\Functional\Application\Service\Validation\Relay;

use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Service\Validation\Relay\RelayCrudValidationService;
use App\Domain\Relay\Service\RelayCrudService;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\Examples;
use Codeception\Example;
use Symfony\Component\Validator\ConstraintViolationList;

class RelayCrudValidationServiceCest
{
    #[Examples('relay')]
    #[Examples('swift')]
    public function positiveValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudRelayDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->commandOn = $I->faker()->word();
        $dto->commandOff = $I->faker()->word();
        $dto->isFeedbackPayload = $I->faker()->word();
        $dto->checkTopic = $I->faker()->word();
        $dto->checkTopicPayloadOn = $I->faker()->word();
        $dto->checkTopicPayloadOff = $I->faker()->word();
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

    #[Examples('relay')]
    #[Examples('swift')]
    public function positiveValidateUpdate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudRelayDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->commandOn = $I->faker()->word();
        $dto->commandOff = $I->faker()->word();
        $dto->isFeedbackPayload = $I->faker()->word();
        $dto->checkTopic = $I->faker()->word();
        $dto->checkTopicPayloadOn = $I->faker()->word();
        $dto->checkTopicPayloadOff = $I->faker()->word();
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

    #[Examples('relay')]
    #[Examples('swift')]
    public function negativeValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudRelayDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->commandOn = $I->faker()->word();
        $dto->commandOff = $I->faker()->word();
        $dto->isFeedbackPayload = $I->faker()->word();
        $dto->checkTopic = $I->faker()->word();
        $dto->checkTopicPayloadOn = $I->faker()->word();
        $dto->checkTopicPayloadOff = $I->faker()->word();
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
        $I->assertEquals('Relay name already exist.', $result[0]->getMessage());
        $I->assertEquals('Relay topic already exist.', $result[1]->getMessage());
    }

    #[Examples('relay')]
    #[Examples('swift')]
    public function positiveValidateUpdateExistEntity(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudRelayDto();

        $dto->type = $example[0];
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->commandOn = $I->faker()->word();
        $dto->commandOff = $I->faker()->word();
        $dto->isFeedbackPayload = $I->faker()->word();
        $dto->checkTopic = $I->faker()->word();
        $dto->checkTopicPayloadOn = $I->faker()->word();
        $dto->checkTopicPayloadOff = $I->faker()->word();
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

    private function getService(FunctionalTester $I): RelayCrudValidationService
    {
        return $I->grabService(RelayCrudValidationService::class);
    }

    private function crudService(FunctionalTester $I): RelayCrudService
    {
        return $I->grabService(RelayCrudService::class);
    }
}
