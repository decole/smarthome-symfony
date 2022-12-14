<?php

namespace App\Tests\functional\Domain\Page\Service;

use App\Application\Http\Web\Page\Dto\CrudPageDto;
use App\Application\Http\Web\Relay\Dto\CrudRelayDto;
use App\Application\Http\Web\Sensor\Dto\CrudSensorDto;
use App\Domain\Page\Entity\Page;
use App\Domain\Page\Service\PageCrudService;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Relay\Enum\RelayTypeEnum;
use App\Domain\Relay\Service\RelayCrudService;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\Sensor\Service\SensorCrudService;
use App\Tests\FunctionalTester;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PageCrudServiceCest
{
    public function validateNewEntity(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([], [
            'name' => $I->faker()->word(),
            'config' => [
                'sensor' => [],
                'relay' => [],
                'security' => [],
                'fireSecurity' => [],
            ],
            'alias' => $I->faker()->word(),
            'icon' => $I->faker()->word(),
            'groupId' => random_int(0, 99),
        ]);

        $dto = $service->createDto($request);

        $violation = $service->validate($dto);

        $I->assertInstanceOf(ConstraintViolationListInterface::class, $violation);
        $I->assertEquals(0, $violation->count());
    }

    public function validateNewEmptyEntity(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $request = new Request([
            'name' => null,
            'config' => [],
            'alias' => $I->faker()->word(),
            'icon' => $I->faker()->word(),
            'groupId' => random_int(0, 99),
        ], []);

        $dto = $service->createDto($request);

        $violation = $service->validate($dto);

        $I->assertEquals(0, $violation->count());
    }

    public function createEmpty(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudPageDto();
        $dto->name = $I->faker()->word();
        $dto->config = [
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(Page::class, [
            'id' => (string)$entity->getId(),
            'name' => $entity->getName(),
        ]);
    }

    public function createWithRelay(FunctionalTester $I): void
    {
        $relay = $this->createRelay($I);

        $service = $this->getService($I);

        $dto = new CrudPageDto();
        $dto->name = $I->faker()->word();
        $dto->config = [
            'sensor' => [],
            'relay' => [
                (string)$relay->getId(),
            ],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $entity */
        $entity = $service->create($dto);

        $I->assertEquals((string)$relay->getId(), $entity->getConfig()['relay'][0]);
        $I->seeInRepository(Page::class, [
            'id' => (string)$entity->getId(),
            'name' => $entity->getName(),
        ]);
    }

    public function createWithSensor(FunctionalTester $I): void
    {
        $sensor = $this->createSensor($I);

        $service = $this->getService($I);

        $dto = new CrudPageDto();
        $dto->name = $I->faker()->word();
        $dto->config = [
            'sensor' => [
                (string)$sensor->getId(),
            ],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $entity */
        $entity = $service->create($dto);

        $I->assertEquals((string)$sensor->getId(), $entity->getConfig()['sensor'][0]);
        $I->seeInRepository(Page::class, [
            'id' => (string)$entity->getId(),
            'name' => $entity->getName(),
        ]);
    }

    public function list(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudPageDto();
        $dto->name = $I->faker()->word();
        $dto->config = [
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $entity */
        $entitySaved = $service->create($dto);

        $result = $service->list();

        $I->assertIsArray($result);

        $i = 0;

        foreach ($result as $entity) {
            $I->assertInstanceOf(Page::class, $entity);

            if ($entity->getId()->toString() === $entitySaved->getId()->toString()) {
                $I->assertEquals($dto->name, $entity->getName());
                $I->assertEquals($dto->config, $entity->getConfig());

                ++$i;
            }
        }

        $I->assertEquals(1, $i, 'Page entity not found in repository');
    }

    public function update(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudPageDto();
        $dto->name = $I->faker()->word();
        $dto->config = [
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(Page::class, [
            'id' => $entity->getId()->toString(),
            'name' => $dto->name,
        ]);

        $dto->name = $name = $I->faker()->word();
        $dto->config = [
            'sensor' => [
                $sensor = Uuid::uuid4(),
            ],
            'relay' => [
                $relay = Uuid::uuid4(),
            ],
            'security' => [
                $security = Uuid::uuid4(),
            ],
            'fireSecurity' => [
                $fireSecurity = Uuid::uuid4(),
            ],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $updatedEntity */
        $updatedEntity = $service->update($entity->getId()->toString(), $dto);

        $I->seeInRepository(Page::class, [
            'id' => $entity->getId()->toString(),
            'name' => $name,
        ]);
        $I->assertEquals($sensor, $updatedEntity->getConfig()['sensor'][0]);
        $I->assertEquals($relay, $updatedEntity->getConfig()['relay'][0]);
        $I->assertEquals($security, $updatedEntity->getConfig()['security'][0]);
        $I->assertEquals($fireSecurity, $updatedEntity->getConfig()['fireSecurity'][0]);
    }

    public function delete(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudPageDto();
        $dto->name = $I->faker()->word();
        $dto->config = [
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(Page::class, [
            'id' => $entity->getId()->toString(),
            'name' => $dto->name,
        ]);

        $service->delete($entity->getId()->toString());

        $I->dontSeeInRepository(Page::class, [
            'id' => $entity->getId()->toString(),
        ]);
    }

    public function entityByDto(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudPageDto();

        $dto->name = $name = $I->faker()->word();
        $dto->config = [
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        /** @var Page $entity */
        $entity = $service->create($dto);

        $I->seeInRepository(Page::class, [
            'id' => $entity->getId()->toString(),
        ]);

        $dtoSaved = $service->entityByDto($entity->getId()->toString());

        $I->assertEquals($name, $dtoSaved->name);
        $I->assertEquals($dto->config, $dtoSaved->config);
    }

    public function getNewEntityByDto(FunctionalTester $I): void
    {
        $service = $this->getService($I);

        $dto = new CrudPageDto();

        $dto->name = $name = $I->faker()->word();
        $dto->config = [
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ];
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        $entity = $service->getNewEntityByDto($dto);

        $I->assertEquals($name, $entity->getName());
        $I->assertEquals($dto->config, $entity->getConfig());
    }

    private function getService(FunctionalTester $I): PageCrudService
    {
        return $I->grabService(PageCrudService::class);
    }

    private function createRelay(FunctionalTester $I): Relay
    {
        $dto = new CrudRelayDto();

        $dto->type = RelayTypeEnum::DRY_RELAY_TYPE->value;
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->commandOn = $I->faker()->word();
        $dto->commandOff = $I->faker()->word();
        $dto->checkTopicPayloadOn = $I->faker()->word();
        $dto->checkTopicPayloadOff = $I->faker()->word();
        $dto->lastCommand = $I->faker()->word();
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $I->grabService(RelayCrudService::class)->create($dto);
    }

    private function createSensor(FunctionalTester $I): Sensor
    {
        $dto = new CrudSensorDto();

        $dto->type = 'temperature';
        $dto->name = $I->faker()->word();
        $dto->topic = $I->faker()->word();
        $dto->payload = $I->faker()->word();
        $dto->payloadMin = 0;
        $dto->payloadMax = 100;
        $dto->message_info = $I->faker()->word();
        $dto->message_ok = $I->faker()->word();
        $dto->message_warn = $I->faker()->word();
        $dto->status = 'on';
        $dto->notify = 'on';

        return $I->grabService(SensorCrudService::class)->create($dto);
    }
}