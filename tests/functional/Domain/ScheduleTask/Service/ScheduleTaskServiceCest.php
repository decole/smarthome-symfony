<?php

namespace App\Tests\functional\Domain\ScheduleTask\Service;

use App\Domain\Identity\Entity\User;
use App\Domain\ScheduleTask\Entity\ScheduleTask;
use App\Domain\ScheduleTask\Input\ScheduleTaskInputDto;
use App\Domain\ScheduleTask\Service\ScheduleTaskService;
use App\Tests\FunctionalTester;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;

use Ramsey\Uuid\Rfc4122\UuidV4;

use ReflectionMethod;

use function Amp\Promise\first;

class ScheduleTaskServiceCest
{
    public function addSimple(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $dto = new ScheduleTaskInputDto(
            command: $I->faker()->word(),
            arguments: [],
            interval: null,
            nextRun: null
        );

        $service->add($dto);

        $I->seeInRepository(ScheduleTask::class, [
            'command' => $dto->command,
        ]);
    }

    public function addWithInterval(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $dto = new ScheduleTaskInputDto(
            command: $I->faker()->word(),
            arguments: [],
            interval: '1 day',
            nextRun: null
        );

        $service->add($dto);

        $I->seeInRepository(ScheduleTask::class, [
            'command' => $dto->command,
            'interval' => $dto->interval
        ]);
    }

    public function addWithNextRun(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $dto = new ScheduleTaskInputDto(
            command: $I->faker()->word(),
            arguments: [],
            interval: null,
            nextRun: (new DateTimeImmutable())->modify('1 day')
        );

        $service->add($dto);

        $I->seeInRepository(ScheduleTask::class, [
            'command' => $dto->command,
            'nextRun' => $dto->nextRun
        ]);
    }

    public function addWithCommandParams(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $dto = new ScheduleTaskInputDto(
            command: $I->faker()->word(),
            arguments: ['lol'=>'kek'],
            interval: null,
            nextRun: null
        );

        $service->add($dto);

        $I->seeInRepository(ScheduleTask::class, [
            'command' => $dto->command,
        ]);

        $tasks = $I->grabEntitiesFromRepository(ScheduleTask::class, ['command' => $dto->command,]);

        $task = $tasks[0] ?? null;

        $I->assertNotNull($task);
        /** @var ScheduleTask $task */
        $I->assertEquals($dto->arguments, $task->getArguments());
    }

    public function addWithAllArguments(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $dto = new ScheduleTaskInputDto(
            command: $I->faker()->word(),
            arguments: [$I->faker()->word() => $I->faker()->word()],
            interval: '1 month',
            nextRun: (new DateTimeImmutable())->modify('1 day')
        );

        $service->add($dto);

        $I->seeInRepository(ScheduleTask::class, [
            'command' => $dto->command,
        ]);

        $tasks = $I->grabEntitiesFromRepository(ScheduleTask::class, ['command' => $dto->command,]);

        $task = $tasks[0] ?? null;

        $I->assertNotNull($task);
        /** @var ScheduleTask $task */
        $I->assertEquals($dto->command, $task->getCommand());
        $I->assertEquals($dto->interval, $task->getInterval());
        $I->assertEquals($dto->nextRun, $task->getNextRun());
    }

    public function delete(FunctionalTester $I): void
    {
        $task = new ScheduleTask(
            command: $command = $I->faker()->word(),
            arguments: [$I->faker()->word() => $I->faker()->word()],
            interval: '1 month',
            lastRun: new DateTimeImmutable(),
            nextRun: (new DateTimeImmutable())->modify('1 day')
        );

        /** @var UuidV4 $id */
        $id = $I->haveInRepository($task, []);

        /** @var EntityManager $userManager */
        $userManager = $I->grabService(EntityManager::class);

        $actualTask = $userManager->find(ScheduleTask::class, (string)$id);

        $I->assertInstanceOf(ScheduleTask::class, $actualTask);

        $I->seeInRepository(ScheduleTask::class, [
            'id' => $id,
            'command' => $command,
        ]);

        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $service->delete($actualTask);

        $I->dontSeeInRepository(ScheduleTask::class, [
            'id' => $id,
            'command' => $command,
        ]);
    }

    public function save(FunctionalTester $I): void
    {
        $task = new ScheduleTask(
            command: $command = $I->faker()->word(),
            arguments: [$I->faker()->word() => $I->faker()->word()],
            interval: $I->faker()->word(),
            lastRun: new DateTimeImmutable(),
            nextRun: (new DateTimeImmutable())->modify('1 day')
        );

        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $service->save($task);

        $I->seeInRepository(ScheduleTask::class, [
            'id' => $task->getId(),
            'command' => $command,
        ]);
    }

    public function getNextDateEmpty(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);
        $I->assertEquals(null, $service->getNextDate(''));
    }

    public function getNextDateCron(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);
        $I->assertTrue($service->getNextDate('* * * * *')->getTimestamp() >= (new DateTimeImmutable())->getTimestamp());
    }

    public function getNextDateString(FunctionalTester $I): void
    {
        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);
        $I->assertTrue((new DateTimeImmutable())->getTimestamp() > $service->getNextDate('-1 day')->getTimestamp());
    }

    public function begin(FunctionalTester $I): void
    {
        $task = new ScheduleTask(
            command: $command = $I->faker()->word(),
            arguments: [$I->faker()->word() => $I->faker()->word()],
            interval: '1 month',
            lastRun: new DateTimeImmutable(),
            nextRun: (new DateTimeImmutable())->modify('1 day')
        );

        /** @var UuidV4 $id */
        $id = $I->haveInRepository($task, []);

        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);
        $reflection = new ReflectionMethod($service, 'begin');
        $reflection->setAccessible(true);
        $reflection->invoke($service, $task);

        $I->seeInRepository(ScheduleTask::class, [
            'id' => $id,
            'command' => $command,
        ]);

        $I->assertNull($task->getNextRun());
    }

    public function endWithInterval(FunctionalTester $I): void
    {
        $task = new ScheduleTask(
            command: $command = $I->faker()->word(),
            arguments: [$I->faker()->word() => $I->faker()->word()],
            interval: '1 month',
            lastRun: new DateTimeImmutable(),
            nextRun: (new DateTimeImmutable())->modify('1 day')
        );

        /** @var UuidV4 $id */
        $id = $I->haveInRepository($task, []);

        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);

        $reflection = new ReflectionMethod($service, 'begin');
        $reflection->setAccessible(true);
        $reflection->invoke($service, $task);

        $reflection = new ReflectionMethod($service, 'end');
        $reflection->setAccessible(true);
        $reflection->invoke($service, $task);

        $I->seeInRepository(ScheduleTask::class, [
            'id' => $id,
            'command' => $command,
        ]);

        $format = 'd.m.Y';
        $expectedDate = (new DateTimeImmutable())->modify($task->getInterval())->format($format);
        $I->assertEquals($expectedDate, $task->getNextRun()->format($format));

        /** @var EntityManager $userManager */
        $userManager = $I->grabService(EntityManager::class);

        $actualTask = $userManager->find(ScheduleTask::class, (string)$id);

        $I->assertEquals($expectedDate, $actualTask->getNextRun()->format($format));
    }

    public function endWithOutInterval(FunctionalTester $I): void
    {
        $task = new ScheduleTask(
            command: $command = $I->faker()->word(),
            arguments: [$I->faker()->word() => $I->faker()->word()],
            interval: null,
            lastRun: new DateTimeImmutable(),
            nextRun: (new DateTimeImmutable())->modify('1 day')
        );

        /** @var UuidV4 $id */
        $id = $I->haveInRepository($task, []);

        /** @var ScheduleTaskService $service */
        $service = $I->grabService(ScheduleTaskService::class);
        $reflection = new ReflectionMethod($service, 'end');
        $reflection->setAccessible(true);
        $reflection->invoke($service, $task);

        $I->seeInRepository(ScheduleTask::class, [
            'id' => $id,
            'command' => $command,
        ]);

        /** @var EntityManager $userManager */
        $userManager = $I->grabService(EntityManager::class);

        $actualTask = $userManager->find(ScheduleTask::class, (string)$id);

        $I->assertEquals(null, $task->getNextRun());
        $I->assertEquals(null, $actualTask->getNextRun());
    }
}