<?php

namespace App\Tests\unit\Infrastructure\Doctrine\Service\Relay;

use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use App\Tests\UnitTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;

class RelayCrudServiceCest
{
  public function getTypes(UnitTester $I): void
    {
        $service = $this->getService($I);

        $I->assertEquals(Relay::RELAY_TYPES, $service->getTypes());
    }

    public function createEmptyDto(UnitTester $I): void
    {
        $service = $this->getService($I);

        $dto = $service->createDto(null);

        $I->assertEquals(Relay::DRY_RELAY_TYPE, $dto->type);
        $I->assertEquals(null, $dto->name);
        $I->assertEquals(null, $dto->topic);
        $I->assertEquals(null, $dto->payload);
        $I->assertEquals(null, $dto->commandOn);
        $I->assertEquals(null, $dto->commandOff);
        $I->assertEquals(null, $dto->isFeedbackPayload);
        $I->assertEquals(null, $dto->checkTopic);
        $I->assertEquals(null, $dto->checkTopicPayloadOn);
        $I->assertEquals(null, $dto->checkTopicPayloadOff);
        $I->assertEquals(null, $dto->lastCommand);
        $I->assertEquals(null, $dto->message_info);
        $I->assertEquals(null, $dto->message_ok);
        $I->assertEquals(null, $dto->message_warn);
        $I->assertEquals(null, $dto->status);
        $I->assertEquals(null, $dto->notify);
    }

    /**
     * @param UnitTester $I
     * @param Example $example
     * @example(type="relay")
     * @example(type="swift")
     * @return void
     */
    public function createByRequestDto(UnitTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'type' => $type = $example['type'],
            'name' => $name = $I->faker()->word(),
            'topic' => $topic = $I->faker()->word(),
            'payload' => $payload = $I->faker()->word(),
            'commandOn' => $commandOn = $I->faker()->word(),
            'commandOff' => $commandOff = $I->faker()->word(),
            'isFeedbackPayload' => $isFeedbackPayload = $I->faker()->word(),
            'checkTopic' => $checkTopic = $I->faker()->word(),
            'checkTopicPayloadOn' => $checkTopicPayloadOn = $I->faker()->word(),
            'checkTopicPayloadOff' => $checkTopicPayloadOff = $I->faker()->word(),
            'lastCommand' => $lastCommand = $I->faker()->word(),
            'message_info' => $messageInfo = $I->faker()->word(),
            'message_ok' => $messageOk = $I->faker()->word(),
            'message_warn' => $messageWarn = $I->faker()->word(),
            'status' => $status = 'on',
            'notify' => $notify = 'on',
        ]);

        $dto = $service->createDto($request);

        $I->assertEquals($type, $dto->type);
        $I->assertEquals($name, $dto->name);
        $I->assertEquals($topic, $dto->topic);
        $I->assertEquals($payload, $dto->payload);
        $I->assertEquals($commandOn, $dto->commandOn);
        $I->assertEquals($commandOff, $dto->commandOff);
        $I->assertEquals($isFeedbackPayload, $dto->isFeedbackPayload);
        $I->assertEquals($checkTopic, $dto->checkTopic);
        $I->assertEquals($checkTopicPayloadOn, $dto->checkTopicPayloadOn);
        $I->assertEquals($checkTopicPayloadOff, $dto->checkTopicPayloadOff);
        $I->assertEquals($lastCommand, $dto->lastCommand);
        $I->assertEquals($messageInfo, $dto->message_info);
        $I->assertEquals($messageOk, $dto->message_ok);
        $I->assertEquals($messageWarn, $dto->message_warn);
        $I->assertEquals($status, $dto->status);
        $I->assertEquals($notify, $dto->notify);
    }

    private function getService(UnitTester $I): RelayCrudService
    {
        return $I->grabService(RelayCrudService::class);
    }
}