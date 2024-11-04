<?php

namespace App\Tests\functional\Domain\VisualNotification\Service;

use App\Application\Http\Web\VisualNotification\Dto\VisualNotificationHistoryInputDto;
use App\DataFixtures\VisualNotificationFixture;
use App\Domain\VisualNotification\Entity\VisualNotification;
use App\Domain\VisualNotification\Service\VisualNotificationHistoryService;
use App\Tests\FunctionalTester;

class VisualNotificationHistoryServiceCest
{
    public function checkSaveNotify(FunctionalTester $I): void
    {
        $notify = new VisualNotification(
            VisualNotification::MESSAGE_TYPE,
            $message = $I->faker()->word()
        );

        $id = $I->haveInRepository($notify);

        $I->seeInRepository(VisualNotification::class, [
            'id' => $id,
            'type' => VisualNotification::MESSAGE_TYPE,
            'message' => $message,
        ]);
    }

    public function firstPage(FunctionalTester $I): void
    {
        $I->clearEntityManager();

        $I->loadFixtures(VisualNotificationFixture::class);

        $I->seeInRepository(VisualNotification::class, [
            'type' => VisualNotification::MESSAGE_TYPE,
            'message' => 'test',
        ]);

        $service = $this->getService($I);

        $dto = new VisualNotificationHistoryInputDto();
        $dto->page = 1;

        $result = $service->paginate($dto);

        $I->assertEquals(1, $result->current);
        $I->assertEquals(1, $result->prev);
        $I->assertEquals(1, $result->count);
        $I->assertEquals(2, $result->next);
        $I->assertEquals(1, count($result->collection));
    }

    public function outOfRangePage(FunctionalTester $I): void
    {
        $I->clearEntityManager();

        $notify = new VisualNotification(VisualNotification::ALERT_TYPE, $message = $I->faker()->word());

        $id = $I->haveInRepository($notify);

        $I->seeInRepository(VisualNotification::class, [
            'id' => $id,
            'type' => VisualNotification::ALERT_TYPE,
            'message' => $message,
        ]);

        $service = $this->getService($I);

        $dto = new VisualNotificationHistoryInputDto();
        $dto->page = 2;

        $result = $service->paginate($dto);

        $I->assertEquals(2, $result->current);
        $I->assertEquals(1, $result->prev);
        $I->assertEquals(1, $result->count);
        $I->assertEquals(3, $result->next);
        $I->assertEquals(0, count($result->collection));
    }

    public function emptyList(FunctionalTester $I): void
    {
        $I->clearEntityManager();

        $service = $this->getService($I);

        $dto = new VisualNotificationHistoryInputDto();
        $dto->page = 1;

        $dto = $service->paginate($dto);

        $I->assertEquals(1, $dto->current);
        $I->assertEquals(1, $dto->prev);
        $I->assertEquals(1, $dto->count);
        $I->assertEquals(2, $dto->next);
        $I->assertEquals(0, count($dto->collection));
    }

    private function getService(FunctionalTester $I): VisualNotificationHistoryService
    {
        return $I->grabService(VisualNotificationHistoryService::class);
    }
}