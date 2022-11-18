<?php

namespace App\Tests\functional\Application\Service\Validation\Page;

use App\Application\Http\Web\Page\Dto\CrudPageDto;
use App\Application\Service\Validation\Page\PageCrudValidationService;
use App\Infrastructure\Doctrine\Service\Page\PageCrudService;
use App\Tests\FunctionalTester;
use Symfony\Component\Validator\ConstraintViolationList;

class PageCrudValidationServiceCest
{
    public function positiveValidateCreate(FunctionalTester $I): void
    {
        $dto = new CrudPageDto();

        $dto->name = $I->faker()->word();
        $dto->config = $I->faker()->shuffleArray([
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ]);

        $service = $this->getService($I);

        $service->setValue($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(false);

        $I->assertEquals(0, $result->count());
    }

    public function positiveValidateUpdate(FunctionalTester $I): void
    {
        $dto = new CrudPageDto();

        $dto->name = $I->faker()->word();
        $dto->config = $I->faker()->shuffleArray([
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ]);

        $service = $this->getService($I);

        $service->setValue($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    public function negativeValidateCreate(FunctionalTester $I): void
    {
        $dto = new CrudPageDto();

        $dto->name = $I->faker()->word();
        $dto->config = $I->faker()->shuffleArray([
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ]);

        $service = $this->getService($I);

        $service->setValue($dto);

        $this->crudService($I)->create($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(false);

        $I->assertEquals(1, $result->count());
        $I->assertEquals('Page name already exist.', $result[0]->getMessage());
    }

    public function positiveValidateUpdateExistEntity(FunctionalTester $I): void
    {
        $dto = new CrudPageDto();

        $dto->name = $I->faker()->word();
        $dto->config = $I->faker()->shuffleArray([
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ]);

        $service = $this->getService($I);

        $service->setValue($dto);

        $this->crudService($I)->create($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    private function getService(FunctionalTester $I): PageCrudValidationService
    {
        return $I->grabService(PageCrudValidationService::class);
    }

    private function crudService(FunctionalTester $I): PageCrudService
    {
        return $I->grabService(PageCrudService::class);
    }
}