<?php

namespace App\Tests\functional\Infrastructure\Doctrine\Service\Security;

use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Infrastructure\Doctrine\Service\Security\SecurityCrudService;
use App\Tests\FunctionalTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SecurityCrudServiceCest
{
    public function validateNewEntity(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'name' => $I->faker()->word(),
            'topic' => $I->faker()->word(),
            'payload' => $I->faker()->word(),
            'detectPayload' => $I->faker()->word(),
            'holdPayload' => $I->faker()->word(),
            'lastCommand' => $I->faker()->word(),
            'params' => [],
            'message_info' => $I->faker()->word(),
            'message_ok' => $I->faker()->word(),
            'message_warn' => $I->faker()->word(),
            'status' => $I->faker()->word(),
            'notify' => $I->faker()->word(),
        ]);

        $dto = $service->createSecurityDto($request);

        $violation = $service->validate($dto);

        $I->assertInstanceOf(ConstraintViolationListInterface::class, $violation);
        $I->assertEquals(0, $violation->count());
    }

    public function negativeValidateNewEntity(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], []);

        $dto = $service->createSecurityDto($request);

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
        $I->assertEquals('detectPayload', $validateFive->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateSix->getMessage());
        $I->assertEquals('detectPayload', $validateSix->getPropertyPath());
        $I->assertEquals('This value should not be blank.', $validateSeven->getMessage());
        $I->assertEquals('holdPayload', $validateSeven->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateEight->getMessage());
        $I->assertEquals('holdPayload', $validateEight->getPropertyPath());
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(status="on",statusRepo="1",type="mqtt_security_device")
     * @example(status="on",statusRepo="1",type="api_security_device")
     * @example(status="off",statusRepo="0",type="mqtt_security_device")
     * @example(status="off",statusRepo="0",type="api_security_device")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSecurityDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->detectPayload = $detectPayload = $I->faker()->word();
        $dto->holdPayload = $holdPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var Security $entity */
        $entity = $service->create($dto);
        $I->assertEquals($notify, $entity->isNotify());

        $I->seeInRepository(Security::class, [
            'id' => $entity->getId()->toString(),
            'securityType' => $type,
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'detectPayload' => $detectPayload,
            'holdPayload' => $holdPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => (int)$example['statusRepo'],
            'notify' => $notify,
        ]);
    }

    /**
     * @param FunctionalTester $I
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @param Example $example
     * @example(status="on",statusRepo="1",type="mqtt_security_device")
     * @example(status="off",statusRepo="0",type="api_security_device")
     * @example(status="on",statusRepo="1",type="api_security_device")
     * @example(status="off",statusRepo="0",type="mqtt_security_device")
     */
    public function list(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSecurityDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->detectPayload = $detectPayload = $I->faker()->word();
        $dto->holdPayload = $holdPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var Security $entitySaved */
        $entitySaved = $service->create($dto);

        $result = $service->list();

        $I->assertIsArray($result);

        $i = 0;

        foreach ($result as $entity) {
            $I->assertInstanceOf(Security::class, $entity);

            if ($entity->getId()->toString() === $entitySaved->getId()->toString()) {
                $I->assertEquals($name, $entity->getName());
                $I->assertEquals($topic, $entity->getTopic());
                $I->assertEquals($type, $entity->getType());
                $I->assertEquals($payload, $entity->getPayload());
                $I->assertEquals($detectPayload, $entity->getDetectPayload());
                $I->assertEquals($holdPayload, $entity->getHoldPayload());
                $I->assertEquals($lastCommand, $entity->getLastCommand());
                $I->assertEquals($messageInfo, $entity->getStatusMessage()->getMessageInfo());
                $I->assertEquals($messageOk, $entity->getStatusMessage()->getMessageOk());
                $I->assertEquals($messageWarn, $entity->getStatusMessage()->getMessageWarn());
                $I->assertEquals($example['statusRepo'], $entity->getStatus());
                $I->assertEquals($notify, $entity->isNotify());

                ++$i;
            }
        }

        $I->assertEquals(1, $i, 'Secure entity not found in repository');
    }

    /**
     * @param FunctionalTester $I
     * @return void
     * @param Example $example
     * @example(status="on",statusRepo="1",type="mqtt_security_device")
     * @example(status="on",statusRepo="1",type="api_security_device")
     * @example(status="off",statusRepo="0",type="mqtt_security_device")
     * @example(status="off",statusRepo="0",type="api_security_device")
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSecurityDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->detectPayload = $detectPayload = $I->faker()->word();
        $dto->holdPayload = $holdPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var Security $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(Security::class, [
            'id' => $entity->getId()->toString(),
            'securityType' => $type,
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'detectPayload' => $detectPayload,
            'holdPayload' => $holdPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => (int)$example['statusRepo'],
            'notify' => $notify,
        ]);

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->detectPayload = $detectPayload = $I->faker()->word();
        $dto->holdPayload = $holdPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'off';
        $dto->notify = 'off';

        $service->update($entity->getId()->toString(), $dto);

        $I->seeInRepository(Security::class, [
            'id' => $entity->getId()->toString(),
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'detectPayload' => $detectPayload,
            'holdPayload' => $holdPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => 0,
            'notify' => false,
        ]);
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(status="on",statusRepo="1",type="mqtt_security_device")
     * @example(status="on",statusRepo="1",type="api_security_device")
     * @example(status="off",statusRepo="0",type="mqtt_security_device")
     * @example(status="off",statusRepo="0",type="api_security_device")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSecurityDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->detectPayload = $detectPayload = $I->faker()->word();
        $dto->holdPayload = $holdPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var Security $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(Security::class, [
            'id' => $entity->getId()->toString(),
            'securityType' => $type,
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'detectPayload' => $detectPayload,
            'holdPayload' => $holdPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => (int)$example['statusRepo'],
            'notify' => $notify,
        ]);

        $service->delete($entity->getId()->toString());

        $I->dontSeeInRepository(Security::class, [
            'id' => $entity->getId()->toString(),
        ]);
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(status="on",statusRepo="1",type="mqtt_security_device")
     * @example(status="off",statusRepo="0",type="mqtt_security_device")
     * @example(status="on",statusRepo="1",type="api_security_device")
     * @example(status="off",statusRepo="0",type="api_security_device")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function entityByDto(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSecurityDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->detectPayload = $detectPayload = $I->faker()->word();
        $dto->holdPayload = $holdPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        /** @var Security $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(Security::class, [
            'id' => $entity->getId()->toString(),
        ]);

        $dtoSaved = $service->entityByDto($entity->getId()->toString());

        $I->assertEquals($name, $dtoSaved->name);
        $I->assertEquals($topic, $dtoSaved->topic);
        $I->assertEquals($type, $dtoSaved->type);
        $I->assertEquals($payload, $dtoSaved->payload);
        $I->assertEquals($detectPayload, $dtoSaved->detectPayload);
        $I->assertEquals($holdPayload, $dtoSaved->holdPayload);
        $I->assertEquals($lastCommand, $dtoSaved->lastCommand);
        $I->assertEquals($messageInfo, $dtoSaved->message_info);
        $I->assertEquals($messageOk, $dtoSaved->message_ok);
        $I->assertEquals($messageWarn, $dtoSaved->message_warn);
        $I->assertEquals($example['status'] === 'on' ? 'on' : null, $dtoSaved->status);
        $I->assertEquals($example['status'] === 'on' ? 'on' : null, $dtoSaved->notify);
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(status="on",statusRepo="1",type="mqtt_security_device")
     * @example(status="off",statusRepo="0",type="api_security_device")
     * @example(status="on",statusRepo="1",type="api_security_device")
     * @example(status="off",statusRepo="0",type="mqtt_security_device")
     * @return void
     */
    public function getNewEntityByDto(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSecurityDto();

        $dto->type = $type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->detectPayload = $detectPayload = $I->faker()->word();
        $dto->holdPayload = $holdPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        $entity = $service->getNewEntityByDto($dto);

        $I->assertEquals($name, $entity->getName());
        $I->assertEquals($topic, $entity->getTopic());
        $I->assertEquals($type, $entity->getType());
        $I->assertEquals($payload, $entity->getPayload());
        $I->assertEquals($detectPayload, $entity->getDetectPayload());
        $I->assertEquals($holdPayload, $entity->getHoldPayload());
        $I->assertEquals($lastCommand, $entity->getLastCommand());
        $I->assertEquals($messageInfo, $entity->getStatusMessage()->getMessageInfo());
        $I->assertEquals($messageOk, $entity->getStatusMessage()->getMessageOk());
        $I->assertEquals($messageWarn, $entity->getStatusMessage()->getMessageWarn());
        $I->assertEquals($example['statusRepo'], $entity->getStatus());
        $I->assertEquals($notify, $entity->isNotify());
    }

    private function getService(FunctionalTester $I): SecurityCrudService
    {
        return $I->grabService(SecurityCrudService::class);
    }
}