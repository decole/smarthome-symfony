<?php

declare(strict_types=1);

namespace App\Tests\Functional\Application\Service\Validation\Profile;

use App\Application\Http\Web\Profile\Dto\CrudProfileDto;
use App\Application\Service\Validation\Profile\ProfileCrudValidationService;
use App\Domain\Identity\Entity\User;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\Skip;
use Codeception\Example;

#[Skip('This test not support new version codeception')]
class ProfileCrudValidationServiceCest
{
    /**
     * @example(flag="0")
     * @example(flag="1")
     *
     * @throws \Exception
     */
    public function positiveValidateCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudProfileDto();

        $dto->login = $I->faker()->word();
        $dto->email = $I->faker()->email();
        $dto->telegramId = random_int(10000000, 99999999);
        $dto->isChangePassword = (bool) $example['flag'];
        $dto->password = $dto->passwordAgan = $I->faker()->password();

        $service = $this->getService($I);
        $service->setValue($dto);

        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    /**
     * @example(password="asdkjhg1jh2g3xz",repassword="asdkjhg1jh2g3xz",expected=0)
     * @example(password="asdkjhg1jh2g3xz",repassword="321kjhg1jh2g3xz",expected=1)
     * @example(password="",repassword="",expected=1)
     *
     * @throws \Exception
     */
    public function positiveValidateWithChangePasswordCreate(FunctionalTester $I, Example $example): void
    {
        $dto = new CrudProfileDto();

        $dto->login = $I->faker()->word();
        $dto->email = $I->faker()->email();
        $dto->telegramId = random_int(10000000, 99999999);
        $dto->isChangePassword = true;
        $dto->password = $example['password'];
        $dto->passwordAgan = $example['repassword'];

        $service = $this->getService($I);
        $service->setValue($dto);

        $result = $service->validate(true);

        $I->assertEquals(0, $result->count());
    }

    private function getService(FunctionalTester $I): ProfileCrudValidationService
    {
        return $I->grabService(ProfileCrudValidationService::class);
    }

    private function getUser(FunctionalTester $I): User
    {
        $user = new User();
        $user->setTelegramId(random_int(10000000, 99999999));
        $user->setEmail($I->faker()->email());
        $user->setName($I->faker()->word());
        $user->setRoles([]);
        $user->setVerified();
        $user->setPassword($I->faker()->word());

        return $user;
    }
}
