<?php

namespace App\Tests\functional\Infrastructure\Doctrine\Service\Relay;

use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use App\Tests\FunctionalTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RelayCrudServiceCest
{
    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(type="relay")
     * @example(type="swift")
     * @return void
     */
    public function validateNewEntity(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'type' => $example['type'],
            'name' => $I->faker()->word,
            'topic' => $I->faker()->word,
            'payload' => $I->faker()->word,
            'commandOn' => $I->faker()->word,
            'commandOff' => $I->faker()->word,
            'checkTopic' => $I->faker()->word,
            'checkTopicPayloadOn' => $I->faker()->word,
            'checkTopicPayloadOff' => $I->faker()->word,
            'lastCommand' => $I->faker()->word,
            'message_info' => $I->faker()->word,
            'message_ok' => $I->faker()->word,
            'message_warn' => $I->faker()->word,
            'status' => $I->faker()->word,
            'notify' => $I->faker()->word,
        ]);

        $dto = $service->createDto($request);

        $violation = $service->validate($dto);

        $I->assertInstanceOf(ConstraintViolationListInterface::class, $violation);
        $I->assertEquals(0, $violation->count());
    }

    public function negativeValidateNewEntity(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], []);

        $dto = $service->createDto($request);

        $violation = $service->validate($dto);

        /** @var \Symfony\Component\Validator\ConstraintViolation $validateOne */
        $validateOne = $violation[0];
        $validateTwo = $violation[1];
        $validateThree = $violation[2];
        $validateFore = $violation[3];
        $validateFive = $violation[4];
        $validateSix = $violation[5];
        $validateSeven = $violation[6];
        $validateEight = $violation[7];

        $I->assertEquals(8, $violation->count());
        $I->assertInstanceOf(ConstraintViolationListInterface::class, $violation);
        $I->assertEquals('This value should not be blank.', $validateOne->getMessage());
        $I->assertEquals('name', $validateOne->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateTwo->getMessage());
        $I->assertEquals('name', $validateTwo->getPropertyPath());
        $I->assertEquals('This value should not be blank.', $validateThree->getMessage());
        $I->assertEquals('topic', $validateThree->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateFore->getMessage());
        $I->assertEquals('topic', $validateFore->getPropertyPath());
        $I->assertEquals('This value should not be blank.', $validateFive->getMessage());
        $I->assertEquals('commandOn', $validateFive->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateSix->getMessage());
        $I->assertEquals('commandOn', $validateSix->getPropertyPath());
        $I->assertEquals('This value should not be blank.', $validateSeven->getMessage());
        $I->assertEquals('commandOff', $validateSeven->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateEight->getMessage());
        $I->assertEquals('commandOff', $validateEight->getPropertyPath());
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(status="on",statusRepo="1",type="relay")
     * @example(status="on",statusRepo="1",type="swift")
     * @example(status="off",statusRepo="0",type="relay")
     * @example(status="off",statusRepo="0",type="swift")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudRelayDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word;
        $dto->topic = $topic = $I->faker()->word;
        $dto->payload = $payload = $I->faker()->word;
        $dto->commandOn = $commandOn = $I->faker()->word;
        $dto->commandOff = $commandOff = $I->faker()->word;
        $dto->checkTopicPayloadOn = $checkTopicPayloadOn = $I->faker()->word;
        $dto->checkTopicPayloadOff = $checkTopicPayloadOff = $I->faker()->word;
        $dto->lastCommand = $lastCommand = $example['status'];
        $dto->message_info = $messageInfo = $I->faker()->word;
        $dto->message_ok = $messageOk = $I->faker()->word;
        $dto->message_warn = $messageWarn = $I->faker()->word;
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var Relay $entity */
        $entity = $service->create($dto);

        $I->assertEquals($notify, $entity->isNotify());

        $I->seeInRepository(Relay::class, [
            'id' => $entity->getId()->toString(),
            'type' => $type,
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'commandOn' => $commandOn,
            'commandOff' => $commandOff,
            'checkTopicPayloadOn' => $checkTopicPayloadOn,
            'checkTopicPayloadOff' => $checkTopicPayloadOff,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => (int)$example['statusRepo'],
        ]);
    }

    /**
     * @param FunctionalTester $I
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @param Example $example
     * @example(status="on",statusRepo="1",type="relay")
     * @example(status="on",statusRepo="1",type="swift")
     * @example(status="off",statusRepo="0",type="relay")
     * @example(status="off",statusRepo="0",type="swift")
     */
    public function list(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudRelayDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word;
        $dto->topic = $topic = $I->faker()->word;
        $dto->payload = $payload = $I->faker()->word;
        $dto->commandOn = $commandOn = $I->faker()->word;
        $dto->commandOff = $commandOff = $I->faker()->word;
        $dto->checkTopicPayloadOn = $checkTopicPayloadOn = $I->faker()->word;
        $dto->checkTopicPayloadOff = $checkTopicPayloadOff = $I->faker()->word;
        $dto->lastCommand = $lastCommand = $example['status'];
        $dto->message_info = $messageInfo = $I->faker()->word;
        $dto->message_ok = $messageOk = $I->faker()->word;
        $dto->message_warn = $messageWarn = $I->faker()->word;
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var Relay $entitySaved */
        $entitySaved = $service->create($dto);

        $result = $service->list();

        $I->assertIsArray($result);

        $i = 0;

        foreach ($result as $entity) {
            $I->assertInstanceOf(Relay::class, $entity);

            if ($entity->getId()->toString() === $entitySaved->getId()->toString()) {
                $I->assertEquals($type, $entity->getType());
                $I->assertEquals($name, $entity->getName());
                $I->assertEquals($topic, $entity->getTopic());
                $I->assertEquals($payload, $entity->getPayload());
                $I->assertEquals($commandOn, $entity->getCommandOn());
                $I->assertEquals($commandOff, $entity->getCommandOff());
                $I->assertEquals($checkTopicPayloadOn, $entity->getCheckTopicPayloadOn());
                $I->assertEquals($checkTopicPayloadOff, $entity->getCheckTopicPayloadOff());
                $I->assertEquals($lastCommand, $entity->getLastCommand());
                $I->assertEquals($messageInfo, $entity->getStatusMessage()->getMessageInfo());
                $I->assertEquals($messageOk, $entity->getStatusMessage()->getMessageOk());
                $I->assertEquals($messageWarn, $entity->getStatusMessage()->getMessageWarn());
                $I->assertEquals($example['statusRepo'], $entity->getStatus());
                $I->assertEquals($notify, $entity->isNotify());

                ++$i;
            }
        }

        $I->assertEquals(1, $i, 'FireSecure entity not found in repository');
    }
//
//    /**
//     * @param FunctionalTester $I
//     * @return void
//     * @throws \Doctrine\ORM\Exception\ORMException
//     * @throws \Doctrine\ORM\OptimisticLockException
//     */
//    public function update(FunctionalTester $I): void
//    {
//        $service = $this->getService($I);
//
//        $dto = new CrudRelayDto();
//
//        $dto->name = $name = $I->faker()->word;
//        $dto->topic = $topic = $I->faker()->word;
//        $dto->payload = $payload = $I->faker()->word;
//        $dto->normalPayload = $normalPayload = $I->faker()->word;
//        $dto->alertPayload = $alertPayload = $I->faker()->word;
//        $dto->lastCommand = $lastCommand = $I->faker()->word;
//        $dto->message_info = $messageInfo = $I->faker()->word;
//        $dto->message_ok = $messageOk = $I->faker()->word;
//        $dto->message_warn = $messageWarn = $I->faker()->word;
//        $dto->status = 'on';
//        $dto->notify = 'on';
//
//        /** @var Relay $entity */
//        $entity = $service->create($dto);
//
//        $I->seeInRepository(Relay::class, [
//            'id' => $entity->getId()->toString(),
//            'name' => $name,
//            'topic' => $topic,
//            'payload' => $payload,
//            'normalPayload' => $normalPayload,
//            'alertPayload' => $alertPayload,
//            'lastCommand' => $lastCommand,
//            'statusMessage.message_info' => $messageInfo,
//            'statusMessage.message_ok' => $messageOk,
//            'statusMessage.message_warn' => $messageWarn,
//            'status' => 1,
//            'notify' => true,
//        ]);
//
//        $dto->name = $name = $I->faker()->word;
//        $dto->topic = $topic = $I->faker()->word;
//        $dto->payload = $payload = $I->faker()->word;
//        $dto->normalPayload = $normalPayload = $I->faker()->word;
//        $dto->alertPayload = $alertPayload = $I->faker()->word;
//        $dto->lastCommand = $lastCommand = $I->faker()->word;
//        $dto->message_info = $messageInfo = $I->faker()->word;
//        $dto->message_ok = $messageOk = $I->faker()->word;
//        $dto->message_warn = $messageWarn = $I->faker()->word;
//        $dto->status = 'off';
//        $dto->notify = 'off';
//
//        $service->update($entity->getId()->toString(), $dto);
//
//        $I->seeInRepository(Relay::class, [
//            'id' => $entity->getId()->toString(),
//            'name' => $name,
//            'topic' => $topic,
//            'payload' => $payload,
//            'normalPayload' => $normalPayload,
//            'alertPayload' => $alertPayload,
//            'lastCommand' => $lastCommand,
//            'statusMessage.message_info' => $messageInfo,
//            'statusMessage.message_ok' => $messageOk,
//            'statusMessage.message_warn' => $messageWarn,
//            'status' => 0,
//            'notify' => false,
//        ]);
//    }
//
//    public function delete(FunctionalTester $I): void
//    {
//        $service = $this->getService($I);
//
//        $dto = new CrudRelayDto();
//
//        $dto->name = $name = $I->faker()->word;
//        $dto->topic = $topic = $I->faker()->word;
//        $dto->payload = $payload = $I->faker()->word;
//        $dto->normalPayload = $normalPayload = $I->faker()->word;
//        $dto->alertPayload = $alertPayload = $I->faker()->word;
//        $dto->lastCommand = $lastCommand = $I->faker()->word;
//        $dto->message_info = $messageInfo = $I->faker()->word;
//        $dto->message_ok = $messageOk = $I->faker()->word;
//        $dto->message_warn = $messageWarn = $I->faker()->word;
//        $dto->status = 'on';
//        $dto->notify = 'on';
//
//        /** @var Relay $entity */
//        $entity = $service->create($dto);
//
//        $I->seeInRepository(Relay::class, [
//            'id' => $entity->getId()->toString(),
//            'name' => $name,
//            'topic' => $topic,
//            'payload' => $payload,
//            'normalPayload' => $normalPayload,
//            'alertPayload' => $alertPayload,
//            'lastCommand' => $lastCommand,
//            'statusMessage.message_info' => $messageInfo,
//            'statusMessage.message_ok' => $messageOk,
//            'statusMessage.message_warn' => $messageWarn,
//            'status' => 1,
//            'notify' => true,
//        ]);
//
//        $service->delete($entity->getId()->toString());
//
//        $I->dontSeeInRepository(Relay::class, [
//            'id' => $entity->getId()->toString(),
//        ]);
//    }
//
//    public function entityByDto(FunctionalTester $I): void
//    {
//        $service = $this->getService($I);
//
//        $dto = new CrudRelayDto();
//
//        $dto->name = $name = $I->faker()->word;
//        $dto->topic = $topic = $I->faker()->word;
//        $dto->payload = $payload = $I->faker()->word;
//        $dto->normalPayload = $normalPayload = $I->faker()->word;
//        $dto->alertPayload = $alertPayload = $I->faker()->word;
//        $dto->lastCommand = $lastCommand = $I->faker()->word;
//        $dto->message_info = $messageInfo = $I->faker()->word;
//        $dto->message_ok = $messageOk = $I->faker()->word;
//        $dto->message_warn = $messageWarn = $I->faker()->word;
//        $dto->status = 'on';
//        $dto->notify = 'on';
//
//        /** @var Relay $entity */
//        $entity = $service->create($dto);
//
//        $I->seeInRepository(Relay::class, [
//            'id' => $entity->getId()->toString(),
//        ]);
//
//        $dtoSaved = $service->entityByDto($entity->getId()->toString());
//
//        $I->assertEquals($name, $dtoSaved->name);
//        $I->assertEquals($topic, $dtoSaved->topic);
//        $I->assertEquals($payload, $dtoSaved->payload);
//        $I->assertEquals($normalPayload, $dtoSaved->normalPayload);
//        $I->assertEquals($alertPayload, $dtoSaved->alertPayload);
//        $I->assertEquals($lastCommand, $dtoSaved->lastCommand);
//        $I->assertEquals($messageInfo, $dtoSaved->message_info);
//        $I->assertEquals($messageOk, $dtoSaved->message_ok);
//        $I->assertEquals($messageWarn, $dtoSaved->message_warn);
//        $I->assertEquals('on', $dtoSaved->status);
//        $I->assertEquals('on', $dtoSaved->notify);
//    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(status="on",statusRepo="1",type="relay")
     * @example(status="on",statusRepo="1",type="swift")
     * @example(status="off",statusRepo="0",type="relay")
     * @example(status="off",statusRepo="0",type="swift")
     * @return void
     */
    public function getNewEntityByDto(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudRelayDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word;
        $dto->topic = $topic = $I->faker()->word;
        $dto->payload = $payload = $I->faker()->word;
        $dto->commandOn = $commandOn = $I->faker()->word;
        $dto->commandOff = $commandOff = $I->faker()->word;
        $dto->checkTopicPayloadOn = $checkTopicPayloadOn = $I->faker()->word;
        $dto->checkTopicPayloadOff = $checkTopicPayloadOff = $I->faker()->word;
        $dto->lastCommand = $lastCommand = $example['status'];
        $dto->message_info = $messageInfo = $I->faker()->word;
        $dto->message_ok = $messageOk = $I->faker()->word;
        $dto->message_warn = $messageWarn = $I->faker()->word;
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $entity = $service->getNewEntityByDto($dto);

        $I->assertEquals($type, $entity->getType());
        $I->assertEquals($name, $entity->getName());
        $I->assertEquals($topic, $entity->getTopic());
        $I->assertEquals($payload, $entity->getPayload());
        $I->assertEquals($commandOn, $entity->getCommandOn());
        $I->assertEquals($commandOff, $entity->getCommandOff());
        $I->assertEquals($checkTopicPayloadOn, $entity->getCheckTopicPayloadOn());
        $I->assertEquals($checkTopicPayloadOff, $entity->getCheckTopicPayloadOff());
        $I->assertEquals($lastCommand, $entity->getLastCommand());
        $I->assertEquals($messageInfo, $entity->getStatusMessage()->getMessageInfo());
        $I->assertEquals($messageOk, $entity->getStatusMessage()->getMessageOk());
        $I->assertEquals($messageWarn, $entity->getStatusMessage()->getMessageWarn());
        $I->assertEquals($example['statusRepo'], $entity->getStatus());
        $I->assertEquals((bool)$example['statusRepo'], $entity->isNotify());
    }

    private function getService(FunctionalTester $I): RelayCrudService
    {
        return $I->grabService(RelayCrudService::class);
    }
}