<?php

declare(strict_types=1);

namespace App\Tests\Functional\Application\Service\Validation\Page;

use App\Application\Http\Web\Page\Dto\CrudPageDto;
use App\Application\Service\Validation\Page\PageCrudValidationService;
use App\Domain\Page\Service\PageCrudService;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\Skip;
use Codeception\Example;
use Symfony\Component\Validator\ConstraintViolationList;

#[Skip('This test not support new version codeception')]
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
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

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
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

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
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

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
        $dto->alias = $I->faker()->word();
        $dto->icon = $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        $service = $this->getService($I);

        $service->setValue($dto);

        $this->crudService($I)->create($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    /**
     * @example(type="name")
     * @example(type="config")
     * @example(type="alias")
     * @example(type="icon")
     *
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function negativeValidateCreateWithEmptyInput(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudPageDto();

        $dto->name = 'name' === $example['type'] ? '' : $I->faker()->word();
        $dto->config = 'config' === $example['type'] ? [] : $I->faker()->shuffleArray([
            'sensor' => [],
            'relay' => [],
            'security' => [],
            'fireSecurity' => [],
        ]);
        $dto->alias = 'alias' === $example['type'] ? '' : $I->faker()->word();
        $dto->icon = 'icon' === $example['type'] ? '' : $I->faker()->word();
        $dto->groupId = random_int(0, 99);

        $service = $this->getService($I);

        $service->setValue($dto);

        $this->crudService($I)->create($dto);

        /** @var ConstraintViolationList $result */
        $result = $service->validate(false);

        $I->assertEquals(2, $result->count());
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
