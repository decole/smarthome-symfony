<?php

namespace App\Tests\unit\Domain\Security\Service;

use App\Application\Helper\StringHelper;
use App\Domain\Security\Entity\Security;
use App\Domain\Security\Enum\SecurityTypeEnum;
use App\Domain\Security\Service\SecurityCrudService;
use App\Tests\UnitTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;

class SecurityCrudServiceCest
{
  public function getTypes(UnitTester $I): void
    {
        $service = $this->getService($I);

        $I->assertEquals(SecurityTypeEnum::cases(), $service->getTypes());
    }

    public function createEmptyDto(UnitTester $I): void
    {
        $service = $this->getService($I);

        $dto = $service->createSecurityDto(null);

        $I->assertEquals(SecurityTypeEnum::MQTT_TYPE->value, $dto->type);
        $I->assertEquals(null, $dto->name);
        $I->assertEquals(null, $dto->topic);
        $I->assertEquals(null, $dto->payload);
        $I->assertEquals(null, $dto->detectPayload);
        $I->assertEquals(null, $dto->holdPayload);
        $I->assertEquals(null, $dto->lastCommand);
        $I->assertEquals(null, $dto->params);
        $I->assertEquals(null, $dto->message_info);
        $I->assertEquals(null, $dto->message_ok);
        $I->assertEquals(null, $dto->message_warn);
        $I->assertEquals(null, $dto->status);
        $I->assertEquals(null, $dto->notify);
    }

    /**
     * @param UnitTester $I
     * @param Example $example
     * @example(type="mqtt_security_device")
     * @example(type="api_security_device")
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
            'detectPayload' => $detectPayload = $I->faker()->word(),
            'holdPayload' => $holdPayload = $I->faker()->word(),
            'lastCommand' => $lastCommand = $I->faker()->word(),
            'params' => $params = '{
    "mqtt": {
        "publishTopic": "/warning/sound",
        "payload": 1
    }
}',
            'message_info' => $messageInfo = $I->faker()->word(),
            'message_ok' => $messageOk = $I->faker()->word(),
            'message_warn' => $messageWarn = $I->faker()->word(),
            'status' => $status = $I->faker()->word(),
            'notify' => $notify = $I->faker()->word(),
        ]);

        $dto = $service->createSecurityDto($request);

        $I->assertEquals($type, $dto->type);
        $I->assertEquals($name, $dto->name);
        $I->assertEquals($topic, $dto->topic);
        $I->assertEquals($payload, $dto->payload);
        $I->assertEquals($detectPayload, $dto->detectPayload);
        $I->assertEquals($holdPayload, $dto->holdPayload);
        $I->assertEquals($lastCommand, $dto->lastCommand);
        $I->assertEquals(StringHelper::sanitize($params), $dto->params);
        $I->assertEquals($messageInfo, $dto->message_info);
        $I->assertEquals($messageOk, $dto->message_ok);
        $I->assertEquals($messageWarn, $dto->message_warn);
        $I->assertEquals($status, $dto->status);
        $I->assertEquals($notify, $dto->notify);
    }

    private function getService(UnitTester $I): SecurityCrudService
    {
        return $I->grabService(SecurityCrudService::class);
    }
}