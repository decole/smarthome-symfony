<?php

namespace App\Tests\unit\Domain\Profile\Service;

use App\Domain\Profile\Service\ProfileCrudService;
use App\Tests\UnitTester;
use Symfony\Component\HttpFoundation\Request;

class ProfileCrudServiceCest
{
    public function createDto(UnitTester $I): void
    {
        $request = new Request([], [
            'email' => $email = $I->faker()->email(),
            'telegram_id' => $telegram = random_int(10000000, 99999999),
            'is_change' => $flag = $I->faker()->word(),
            'password' => $password = $I->faker()->word(),
            'password_agan' => $passwordAgan = $I->faker()->word(),
        ]);

        $login = $I->faker()->word();

        $dto = $this->getService($I)->createDto($login, $request);

        $I->assertEquals($login, $dto->login);
        $I->assertEquals($email, $dto->email);
        $I->assertEquals($telegram, $dto->telegramId);
        $I->assertEquals($flag, $dto->isChangePassword);
        $I->assertEquals($password, $dto->password);
        $I->assertEquals($passwordAgan, $dto->passwordAgan);
    }

    public function createEmptyDto(UnitTester $I): void
    {
        $request = new Request([], []);
        $login = $I->faker()->word();

        $dto = $this->getService($I)->createDto($login, $request);

        $I->assertEquals($login, $dto->login);
        $I->assertEquals(null, $dto->email);
        $I->assertEquals(null, $dto->telegramId);
        $I->assertEquals(null, $dto->isChangePassword);
        $I->assertEquals(null, $dto->password);
        $I->assertEquals(null, $dto->passwordAgan);
    }

    private function getService(UnitTester $I): ProfileCrudService
    {
        return $I->grabService(ProfileCrudService::class);
    }
}