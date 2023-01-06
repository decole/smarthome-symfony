<?php

namespace App\Tests\functional\Domain\Sensor\Service;

use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Sensor\Entity\DryContactSensor;
use App\Domain\Sensor\Entity\HumiditySensor;
use App\Domain\Sensor\Entity\LeakageSensor;
use App\Domain\Sensor\Entity\PressureSensor;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\Sensor\Entity\TemperatureSensor;
use App\Domain\Sensor\Service\SensorCrudService;
use App\Tests\FunctionalTester;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SensorCrudServiceCest
{
    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     * @return void
     */
    public function validateNewEntity(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $request = $this->getRequest($example['type'], $I);

        /** @var CrudSensorDto $dto */
        $dto = $service->createDto($example['type'], $request);

        $violation = $service->validate($dto);

        $I->assertInstanceOf(ConstraintViolationListInterface::class, $violation);
        $I->assertEquals(0, $violation->count());
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     * @return void
     */
    public function negativeValidateNewEntity(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $request = new Request([], []);

        /** @var CrudSensorDto $dto */
        $dto = $service->createDto($example['type'], $request);

        $violation = $service->validate($dto);

        /** @var \Symfony\Component\Validator\ConstraintViolation $validateOne */
        $validateOne = $violation[0];
        $validateTwo = $violation[1];
        $validateThree = $violation[2];
        $validateFore = $violation[3];

        $I->assertEquals(4, $violation->count());
        $I->assertInstanceOf(ConstraintViolationListInterface::class, $violation);
        $I->assertEquals('Значение не должно быть пустым.', $validateOne->getMessage());
        $I->assertEquals('name', $validateOne->getPropertyPath());
        $I->assertEquals('Значение не должно быть null.', $validateTwo->getMessage());
        $I->assertEquals('name', $validateTwo->getPropertyPath());
        $I->assertEquals('Значение не должно быть пустым.', $validateThree->getMessage());
        $I->assertEquals('topic', $validateThree->getPropertyPath());
        $I->assertEquals('Значение не должно быть null.', $validateFore->getMessage());
        $I->assertEquals('topic', $validateFore->getPropertyPath());
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(type="temperature")
     * @ example(type="humidity")
     * @ example(type="leakage")
     * @ example(type="pressure")
     * @ example(type="dryContact")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $notify = true;

        $one = $I->faker()->word();
        $two = $I->faker()->word();
        $specificFields = [];

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $dto->payloadMin = $one;
            $dto->payloadMax = $two;
            $specificFields = [
                'payloadMin' => $one,
                'payloadMax' => $two,
            ];
        }
        if ($example['type'] === 'leakage') {
            $dto->payloadDry = $one;
            $dto->payloadWet = $two;
            $specificFields = [
                'payloadDry' => $one,
                'payloadWet' => $two,
            ];
        }
        if ($example['type'] === 'dryContact') {
            $dto->payloadLow = $one;
            $dto->payloadHigh = $two;
            $specificFields = [
                'payloadLow' => $one,
                'payloadHigh' => $two,
            ];
        }

        /** @var Sensor $entity */
        $entity = $service->create($dto);

        $I->assertEquals($notify, $entity->isNotify());

        $targetClass = $this->getTargetClass($example['type']);

        $I->seeInRepository($targetClass, array_merge(
            [
                'id' => $entity->getId()->toString(),
                'name' => $name,
                'topic' => $topic,
                'payload' => $payload,
                'statusMessage.message_info' => $messageInfo,
                'statusMessage.message_ok' => $messageOk,
                'statusMessage.message_warn' => $messageWarn,
                'status' => 1,
            ],
            $specificFields
        ));
    }

    /**
     * @param FunctionalTester $I
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     */
    public function list(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $one = $I->faker()->word();
        $two = $I->faker()->word();

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $dto->payloadMin = $one;
            $dto->payloadMax = $two;
        }
        if ($example['type'] === 'leakage') {
            $dto->payloadDry = $one;
            $dto->payloadWet = $two;
        }
        if ($example['type'] === 'dryContact') {
            $dto->payloadLow = $one;
            $dto->payloadHigh = $two;
        }

        /** @var Sensor $entity */
        $entitySaved = $service->create($dto);

        $result = $service->list();

        $I->assertIsArray($result);

        $i = 0;

        foreach ($result as $entity) {
            $I->assertInstanceOf(Sensor::class, $entity);

            if ($entity->getId()->toString() === $entitySaved->getId()->toString()) {
                $I->assertEquals($name, $entity->getName());
                $I->assertEquals($topic, $entity->getTopic());
                $I->assertEquals($payload, $entity->getPayload());
                $I->assertEquals($messageInfo, $entity->getStatusMessage()->getMessageInfo());
                $I->assertEquals($messageOk, $entity->getStatusMessage()->getMessageOk());
                $I->assertEquals($messageWarn, $entity->getStatusMessage()->getMessageWarn());
                $I->assertEquals(1, $entity->getStatus());
                $I->assertEquals(true, $entity->isNotify());

                if ($entity->getType() === TemperatureSensor::TYPE ||
                    $entity->getType() === HumiditySensor::TYPE ||
                    $entity->getType() === PressureSensor::TYPE
                ) {
                    /** @var TemperatureSensor|HumiditySensor|PressureSensor $entity */
                    $I->assertEquals($one,$entity->getPayloadMin());
                    $I->assertEquals($two,$entity->getPayloadMax());
                }
                if ($entity->getType() === LeakageSensor::TYPE) {
                    /** @var LeakageSensor $entity */
                    $I->assertEquals($one,$entity->getPayloadDry());
                    $I->assertEquals($two,$entity->getPayloadWet());
                }
                if ($entity->getType() === DryContactSensor::TYPE) {
                    /** @var DryContactSensor $entity */
                    $I->assertEquals($one,$entity->getPayloadLow());
                    $I->assertEquals($two,$entity->getPayloadHigh());
                }

                ++$i;
            }
        }

        $I->assertEquals(1, $i, 'Sensor entity not found in repository');
    }

    /**
     * @param FunctionalTester $I
     * @return void
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(FunctionalTester $I, Example $example): void
    {
        $targetClass = $this->getTargetClass($example['type']);
        $service = $this->getService($I);

        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $one = $I->faker()->word();
        $two = $I->faker()->word();
        $specificFields = [];

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $dto->payloadMin = $one;
            $dto->payloadMax = $two;
            $specificFields = [
                'payloadMin' => $one,
                'payloadMax' => $two,
            ];
        }
        if ($example['type'] === 'leakage') {
            $dto->payloadDry = $one;
            $dto->payloadWet = $two;
            $specificFields = [
                'payloadDry' => $one,
                'payloadWet' => $two,
            ];
        }
        if ($example['type'] === 'dryContact') {
            $dto->payloadLow = $one;
            $dto->payloadHigh = $two;
            $specificFields = [
                'payloadLow' => $one,
                'payloadHigh' => $two,
            ];
        }

        /** @var Sensor $entity */
        $entity = $service->create($dto);

        $I->seeInRepository($targetClass, array_merge(
            [
                'id' => $entity->getId()->toString(),
                'name' => $name,
                'topic' => $topic,
                'payload' => $payload,
                'statusMessage.message_info' => $messageInfo,
                'statusMessage.message_ok' => $messageOk,
                'statusMessage.message_warn' => $messageWarn,
                'status' => 1,
            ],
            $specificFields
        ));

        $one = $I->faker()->word();
        $two = $I->faker()->word();

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $dto->payloadMin = $one;
            $dto->payloadMax = $two;
            $specificFields = [
                'payloadMin' => $one,
                'payloadMax' => $two,
            ];
        }
        if ($example['type'] === 'leakage') {
            $dto->payloadDry = $one;
            $dto->payloadWet = $two;
            $specificFields = [
                'payloadDry' => $one,
                'payloadWet' => $two,
            ];
        }
        if ($example['type'] === 'dryContact') {
            $dto->payloadLow = $one;
            $dto->payloadHigh = $two;
            $specificFields = [
                'payloadLow' => $one,
                'payloadHigh' => $two,
            ];
        }

        $dto->notify = 'off';
        $dto->status = 'off';

        $service->update($entity->getId()->toString(), $dto);

        $I->seeInRepository($targetClass, array_merge(
            [
                'id' => $entity->getId()->toString(),
                'name' => $name,
                'topic' => $topic,
                'payload' => $payload,
                'statusMessage.message_info' => $messageInfo,
                'statusMessage.message_ok' => $messageOk,
                'statusMessage.message_warn' => $messageWarn,
                'status' => 0,
            ],
            $specificFields
        ));
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(FunctionalTester $I, Example $example): void
    {
        $targetClass = $this->getTargetClass($example['type']);
        $service = $this->getService($I);

        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $one = $I->faker()->word();
        $two = $I->faker()->word();
        $specificFields = [];

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $dto->payloadMin = $one;
            $dto->payloadMax = $two;
            $specificFields = [
                'payloadMin' => $one,
                'payloadMax' => $two,
            ];
        }
        if ($example['type'] === 'leakage') {
            $dto->payloadDry = $one;
            $dto->payloadWet = $two;
            $specificFields = [
                'payloadDry' => $one,
                'payloadWet' => $two,
            ];
        }
        if ($example['type'] === 'dryContact') {
            $dto->payloadLow = $one;
            $dto->payloadHigh = $two;
            $specificFields = [
                'payloadLow' => $one,
                'payloadHigh' => $two,
            ];
        }

        /** @var Sensor $entity */
        $entity = $service->create($dto);

        $I->seeInRepository($targetClass, array_merge(
            [
                'id' => $entity->getId()->toString(),
                'name' => $name,
                'topic' => $topic,
                'payload' => $payload,
                'statusMessage.message_info' => $messageInfo,
                'statusMessage.message_ok' => $messageOk,
                'statusMessage.message_warn' => $messageWarn,
                'status' => 1,
            ],
            $specificFields
        ));

        $service->delete($entity->getId()->toString());

        $I->dontSeeInRepository($targetClass, [
            'id' => $entity->getId()->toString(),
        ]);
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function entityByDto(FunctionalTester $I, Example $example): void
    {
        $targetClass = $this->getTargetClass($example['type']);
        $service = $this->getService($I);

        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $one = $I->faker()->word();
        $two = $I->faker()->word();
        $specificFields = [];

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $dto->payloadMin = $one;
            $dto->payloadMax = $two;
            $specificFields = [
                'payloadMin' => $one,
                'payloadMax' => $two,
            ];
        }
        if ($example['type'] === 'leakage') {
            $dto->payloadDry = $one;
            $dto->payloadWet = $two;
            $specificFields = [
                'payloadDry' => $one,
                'payloadWet' => $two,
            ];
        }
        if ($example['type'] === 'dryContact') {
            $dto->payloadLow = $one;
            $dto->payloadHigh = $two;
            $specificFields = [
                'payloadLow' => $one,
                'payloadHigh' => $two,
            ];
        }

        /** @var Sensor $entity */
        $entity = $service->create($dto);

        $I->seeInRepository($targetClass, array_merge(
            [
                'id' => $entity->getId()->toString(),
                'name' => $name,
                'topic' => $topic,
                'payload' => $payload,
                'statusMessage.message_info' => $messageInfo,
                'statusMessage.message_ok' => $messageOk,
                'statusMessage.message_warn' => $messageWarn,
                'status' => 1,
            ],
            $specificFields
        ));

        $dtoSaved = $service->entityByDto($entity->getId()->toString());

        $I->assertEquals($name, $dtoSaved->name);
        $I->assertEquals($topic, $dtoSaved->topic);
        $I->assertEquals($payload, $dtoSaved->payload);
        $I->assertEquals($messageInfo, $dtoSaved->message_info);
        $I->assertEquals($messageOk, $dtoSaved->message_ok);
        $I->assertEquals($messageWarn, $dtoSaved->message_warn);
        $I->assertEquals('on', $dtoSaved->status);
        $I->assertEquals('on', $dtoSaved->notify);

        if ($entity->getType() === TemperatureSensor::TYPE ||
            $entity->getType() === HumiditySensor::TYPE ||
            $entity->getType() === PressureSensor::TYPE
        ) {
            /** @var TemperatureSensor|HumiditySensor|PressureSensor $entity */
            $I->assertEquals($one,$entity->getPayloadMin());
            $I->assertEquals($two,$entity->getPayloadMax());
        }
        if ($entity->getType() === LeakageSensor::TYPE) {
            /** @var LeakageSensor $entity */
            $I->assertEquals($one,$entity->getPayloadDry());
            $I->assertEquals($two,$entity->getPayloadWet());
        }
        if ($entity->getType() === DryContactSensor::TYPE) {
            /** @var DryContactSensor $entity */
            $I->assertEquals($one,$entity->getPayloadLow());
            $I->assertEquals($two,$entity->getPayloadHigh());
        }
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     * @example(type="temperature")
     * @example(type="humidity")
     * @example(type="leakage")
     * @example(type="pressure")
     * @example(type="dryContact")
     * @return void
     */
    public function getNewEntityByDto(FunctionalTester $I, Example $example): void
    {
        $service = $this->getService($I);

        $dto = new CrudSensorDto();

        $dto->type = $example['type'];
        $dto->name = $name = $I->faker()->word();
        $dto->topic = $topic = $I->faker()->word();
        $dto->payload = $payload = $I->faker()->word();
        $dto->message_info = $messageInfo = $I->faker()->word();
        $dto->message_ok = $messageOk = $I->faker()->word();
        $dto->message_warn = $messageWarn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        $one = $I->faker()->word();
        $two = $I->faker()->word();

        if ($example['type'] === 'temperature' ||
            $example['type'] === 'humidity' ||
            $example['type'] === 'pressure'
        ) {
            $dto->payloadMin = $one;
            $dto->payloadMax = $two;
        }
        if ($example['type'] === 'leakage') {
            $dto->payloadDry = $one;
            $dto->payloadWet = $two;
        }
        if ($example['type'] === 'dryContact') {
            $dto->payloadLow = $one;
            $dto->payloadHigh = $two;
        }

        $entity = $service->getNewEntityByDto($dto);

        $I->assertEquals($name, $entity->getName());
        $I->assertEquals($topic, $entity->getTopic());
        $I->assertEquals($payload, $entity->getPayload());
        $I->assertEquals($messageInfo, $entity->getStatusMessage()->getMessageInfo());
        $I->assertEquals($messageOk, $entity->getStatusMessage()->getMessageOk());
        $I->assertEquals($messageWarn, $entity->getStatusMessage()->getMessageWarn());
        $I->assertEquals(1, $entity->getStatus());
        $I->assertEquals(true, $entity->isNotify());

        if ($entity->getType() === TemperatureSensor::TYPE ||
            $entity->getType() === HumiditySensor::TYPE ||
            $entity->getType() === PressureSensor::TYPE
        ) {
            /** @var TemperatureSensor|HumiditySensor|PressureSensor $entity */
            $I->assertEquals($one,$entity->getPayloadMin());
            $I->assertEquals($two,$entity->getPayloadMax());
        }
        if ($entity->getType() === LeakageSensor::TYPE) {
            /** @var LeakageSensor $entity */
            $I->assertEquals($one,$entity->getPayloadDry());
            $I->assertEquals($two,$entity->getPayloadWet());
        }
        if ($entity->getType() === DryContactSensor::TYPE) {
            /** @var DryContactSensor $entity */
            $I->assertEquals($one,$entity->getPayloadLow());
            $I->assertEquals($two,$entity->getPayloadHigh());
        }
    }

    private function getService(FunctionalTester $I): SensorCrudService
    {
        return $I->grabService(SensorCrudService::class);
    }

    /**
     * @param string $type
     * @param FunctionalTester $I
     * @return Request
     */
    private function getRequest(string $type, FunctionalTester $I): Request
    {
        $minMax = [
            'payloadMin' => $I->faker()->word(),
            'payloadMax' => $I->faker()->word(),
        ];
        $dryWet = [
            'payloadDry' => $I->faker()->word(),
            'payloadWet' => $I->faker()->word(),
        ];
        $lowHigh = [
            'payloadHigh' => $I->faker()->word(),
            'payloadLow' => $I->faker()->word(),
        ];

        $typeFields = match ($type) {
            'temperature', 'humidity', 'pressure' => $minMax,
            'leakage' => $dryWet,
            'dryContact' => $lowHigh,
        };

        return new Request(
            query: [],
            request: array_merge(
                [
                    'type' => $type,
                    'name' => $I->faker()->word(),
                    'topic' => $I->faker()->word(),
                    'message_info' => $I->faker()->word(),
                    'message_ok' => $I->faker()->word(),
                    'message_warn' => $I->faker()->word(),
                    'status' => $I->faker()->word(),
                    'notify' => $I->faker()->word(),
                ],
                $typeFields
            )
        );
    }

    /**
     * @param string $type
     * @return string
     */
    private function getTargetClass(string $type): string
    {
        return match ($type) {
            'temperature' => TemperatureSensor::class,
            'humidity' => HumiditySensor::class,
            'pressure' => PressureSensor::class,
            'leakage' => LeakageSensor::class,
            'dryContact' => DryContactSensor::class,
        };
    }
}