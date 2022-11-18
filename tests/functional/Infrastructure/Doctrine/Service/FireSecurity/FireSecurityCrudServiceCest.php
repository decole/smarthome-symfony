<?php

namespace App\Tests\functional\Infrastructure\Doctrine\Service\FireSecurity;

use App\Application\Http\Web\FireSecurity\Dto\CrudFireSecurityDto;
use App\Domain\FireSecurity\Entity\FireSecurity;
use App\Infrastructure\Doctrine\Service\FireSecurity\FireSecurityCrudService;
use App\Tests\FunctionalTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class FireSecurityCrudServiceCest
{
    public function validateNewEntity(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'name' => $I->faker()->word(),
            'topic' => $I->faker()->word(),
            'payload' => $I->faker()->word(),
            'normalPayload' => $I->faker()->word(),
            'alertPayload' => $I->faker()->word(),
            'lastCommand' => $I->faker()->word(),
            'message_info' => $I->faker()->word(),
            'message_ok' => $I->faker()->word(),
            'message_warn' => $I->faker()->word(),
            'status' => $I->faker()->word(),
            'notify' => $I->faker()->word(),
        ]);

        $dto = $service->createFireSecurityDto($request);

        $violation = $service->validate($dto);

        $I->assertInstanceOf(ConstraintViolationListInterface::class, $violation);
        $I->assertEquals(0, $violation->count());
    }

    public function negativeValidateNewEntity(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], []);

        $dto = $service->createFireSecurityDto($request);

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
        $I->assertEquals('normalPayload', $validateFive->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateSix->getMessage());
        $I->assertEquals('normalPayload', $validateSix->getPropertyPath());
        $I->assertEquals('This value should not be blank.', $validateSeven->getMessage());
        $I->assertEquals('alertPayload', $validateSeven->getPropertyPath());
        $I->assertEquals('This value should not be null.', $validateEight->getMessage());
        $I->assertEquals('alertPayload', $validateEight->getPropertyPath());
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(status="on",statusRepo="1")
     * @example(status="off",statusRepo="0")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudFireSecurityDto();

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->normalPayload = $normalPayload = $I->faker()->word();
        $dto->alertPayload = $alertPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var FireSecurity $entity */
        $entity = $service->create($dto);

        $I->assertEquals($notify, $entity->isNotify());

        $I->seeInRepository(FireSecurity::class, [
            'id' => $entity->getId()->toString(),
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'normalPayload' => $normalPayload,
            'alertPayload' => $alertPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => (int)$example['statusRepo'],
            'notify' => $example['status'] === 'on',
        ]);
    }

    /**
     * @param FunctionalTester $I
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @param Example $example
     * @example(status="on",statusRepo="1")
     * @example(status="off",statusRepo="0")
     */
    public function list(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudFireSecurityDto();

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->normalPayload = $normalPayload = $I->faker()->word();
        $dto->alertPayload = $alertPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = $example['status'];
        $dto->notify = $example['status'];

        $notify = $example['status'] === 'on';

        /** @var FireSecurity $entitySaved */
        $entitySaved = $service->create($dto);

        $result = $service->list();

        $I->assertIsArray($result);

        $i = 0;

        foreach ($result as $entity) {
            $I->assertInstanceOf(FireSecurity::class, $entity);

            if ($entity->getId()->toString() === $entitySaved->getId()->toString()) {
                $I->assertEquals($name, $entity->getName());
                $I->assertEquals($topic, $entity->getTopic());
                $I->assertEquals($payload, $entity->getPayload());
                $I->assertEquals($normalPayload, $entity->getNormalPayload());
                $I->assertEquals($alertPayload, $entity->getAlertPayload());
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

    /**
     * @param FunctionalTester $I
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudFireSecurityDto();

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->normalPayload = $normalPayload = $I->faker()->word();
        $dto->alertPayload = $alertPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        /** @var FireSecurity $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(FireSecurity::class, [
            'id' => $entity->getId()->toString(),
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'normalPayload' => $normalPayload,
            'alertPayload' => $alertPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => 1,
            'notify' => true,
        ]);

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->normalPayload = $normalPayload = $I->faker()->word();
        $dto->alertPayload = $alertPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'off';
        $dto->notify = 'off';

        $service->update($entity->getId()->toString(), $dto);

        $I->seeInRepository(FireSecurity::class, [
            'id' => $entity->getId()->toString(),
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'normalPayload' => $normalPayload,
            'alertPayload' => $alertPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => 0,
            'notify' => false,
        ]);
    }

    public function delete(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudFireSecurityDto();

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->normalPayload = $normalPayload = $I->faker()->word();
        $dto->alertPayload = $alertPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        /** @var FireSecurity $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(FireSecurity::class, [
            'id' => $entity->getId()->toString(),
            'name' => $name,
            'topic' => $topic,
            'payload' => $payload,
            'normalPayload' => $normalPayload,
            'alertPayload' => $alertPayload,
            'lastCommand' => $lastCommand,
            'statusMessage.message_info' => $messageInfo,
            'statusMessage.message_ok' => $messageOk,
            'statusMessage.message_warn' => $messageWarn,
            'status' => 1,
            'notify' => true,
        ]);

        $service->delete($entity->getId()->toString());

        $I->dontSeeInRepository(FireSecurity::class, [
            'id' => $entity->getId()->toString(),
        ]);
    }

    public function entityByDto(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudFireSecurityDto();

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->normalPayload = $normalPayload = $I->faker()->word();
        $dto->alertPayload = $alertPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        /** @var FireSecurity $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(FireSecurity::class, [
            'id' => $entity->getId()->toString(),
        ]);

        $dtoSaved = $service->entityByDto($entity->getId()->toString());

        $I->assertEquals($name, $dtoSaved->name);
        $I->assertEquals($topic, $dtoSaved->topic);
        $I->assertEquals($payload, $dtoSaved->payload);
        $I->assertEquals($normalPayload, $dtoSaved->normalPayload);
        $I->assertEquals($alertPayload, $dtoSaved->alertPayload);
        $I->assertEquals($lastCommand, $dtoSaved->lastCommand);
        $I->assertEquals($messageInfo, $dtoSaved->message_info);
        $I->assertEquals($messageOk, $dtoSaved->message_ok);
        $I->assertEquals($messageWarn, $dtoSaved->message_warn);
        $I->assertEquals('on', $dtoSaved->status);
        $I->assertEquals('on', $dtoSaved->notify);
    }

    public function getNewEntityByDto(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudFireSecurityDto();

        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->normalPayload = $normalPayload = $I->faker()->word();
        $dto->alertPayload = $alertPayload = $I->faker()->word();
        $dto->lastCommand = $lastCommand = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $entity = $service->getNewEntityByDto($dto);

        $I->assertEquals($name, $entity->getName());
        $I->assertEquals($topic, $entity->getTopic());
        $I->assertEquals($payload, $entity->getPayload());
        $I->assertEquals($normalPayload, $entity->getNormalPayload());
        $I->assertEquals($alertPayload, $entity->getAlertPayload());
        $I->assertEquals($lastCommand, $entity->getLastCommand());
        $I->assertEquals($messageInfo, $entity->getStatusMessage()->getMessageInfo());
        $I->assertEquals($messageOk, $entity->getStatusMessage()->getMessageOk());
        $I->assertEquals($messageWarn, $entity->getStatusMessage()->getMessageWarn());
        $I->assertEquals(1, $entity->getStatus());
        $I->assertEquals(true, $entity->isNotify());
    }

    private function getService(FunctionalTester $I): FireSecurityCrudService
    {
        return $I->grabService(FireSecurityCrudService::class);
    }
}