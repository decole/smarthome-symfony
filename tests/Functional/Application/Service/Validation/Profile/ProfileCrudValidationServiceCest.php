<?php

declare(strict_types=1);

namespace App\Tests\Functional\Application\Service\Validation\Profile;

use App\Application\Http\Web\Profile\Dto\CrudProfileDto;
use App\Application\Service\Validation\Profile\ProfileCrudValidationService;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\Examples;
use Codeception\Example;

class ProfileCrudValidationServiceCest
{
    #[Examples('0')]
    #[Examples('1')]
    public function positiveValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudProfileDto();

        $dto->login = $I->faker()->word();
        $dto->email = $I->faker()->email();
        $dto->telegramId = random_int(10000000, 99999999);
        $dto->isChangePassword = $example[0];
        $dto->password = $dto->passwordAgan = $I->faker()->password();

        $service = $this->getService($I);
        $service->setValue($dto);

        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    #[Examples('asdkjhg1jh2g3xz', 'asdkjhg1jh2g3xz', '0')]
    #[Examples('asdkjhg1jh2g3xz', '321kjhg1jh2g3xz', '1')]
    #[Examples('', '', '1')]
    public function positiveValidateWithChangePasswordCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudProfileDto();

        $dto->login = $I->faker()->word();
        $dto->email = $I->faker()->email();
        $dto->telegramId = random_int(10000000, 99999999);
        $dto->isChangePassword = '1';
        $dto->password = $example[0];
        $dto->passwordAgan = $example[1];

        $service = $this->getService($I);
        $service->setValue($dto);

        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    private function getService(FunctionalTester $I): ProfileCrudValidationService
    {
        return $I->grabService(ProfileCrudValidationService::class);
    }
}
