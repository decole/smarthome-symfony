<?php

namespace App\Tests\functional\Infrastructure\Doctrine\Service\FireSecurity;

use App\Domain\Doctrine\Security\Entity\Security;
use App\Infrastructure\Doctrine\Service\FireSecurity\FireSecurityCrudService;
use App\Tests\UnitTester;
use Symfony\Component\HttpFoundation\Request;

class FireSecurityCrudServiceFunctionalCest
{
    public function getTypes(UnitTester $I): void
    {
        $service = $this->getService($I);

        $I->assertEquals(Security::SECURITY_TYPES, $service->getTypes());
    }

    public function createEmptyFireSecurityDto(UnitTester $I): void
    {
        $service = $this->getService($I);

        $dto = $service->createFireSecurityDto(null);

        $I->assertEquals(null, $dto->name);
        $I->assertEquals(null, $dto->topic);
        $I->assertEquals(null, $dto->payload);
        $I->assertEquals(null, $dto->normalPayload);
        $I->assertEquals(null, $dto->alertPayload);
        $I->assertEquals(null, $dto->lastCommand);
        $I->assertEquals(null, $dto->message_info);
        $I->assertEquals(null, $dto->message_ok);
        $I->assertEquals(null, $dto->message_warn);
        $I->assertEquals(null, $dto->status);
        $I->assertEquals(null, $dto->notify);
    }

    public function createByRequestFireSecurityDto(UnitTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'name' => $name = $I->faker()->word,
            'topic' => $topic = $I->faker()->word,
            'payload' => $payload = $I->faker()->word,
            'normalPayload' => $normalPayload = $I->faker()->word,
            'alertPayload' => $alertPayload = $I->faker()->word,
            'lastCommand' => $lastCommand = $I->faker()->word,
            'message_info' => $messageInfo = $I->faker()->word,
            'message_ok' => $messageOk = $I->faker()->word,
            'message_warn' => $messageWarn = $I->faker()->word,
            'status' => $status = $I->faker()->word,
            'notify' => $notify = $I->faker()->word,
        ]);

        $dto = $service->createFireSecurityDto($request);

        $I->assertEquals($name, $dto->name);
        $I->assertEquals($topic, $dto->topic);
        $I->assertEquals($payload, $dto->payload);
        $I->assertEquals($normalPayload, $dto->normalPayload);
        $I->assertEquals($alertPayload, $dto->alertPayload);
        $I->assertEquals($lastCommand, $dto->lastCommand);
        $I->assertEquals($messageInfo, $dto->message_info);
        $I->assertEquals($messageOk, $dto->message_ok);
        $I->assertEquals($messageWarn, $dto->message_warn);
        $I->assertEquals($status, $dto->status);
        $I->assertEquals($notify, $dto->notify);
    }

    private function getService(UnitTester $I): FireSecurityCrudService
    {
        return $I->grabService(FireSecurityCrudService::class);
    }
}