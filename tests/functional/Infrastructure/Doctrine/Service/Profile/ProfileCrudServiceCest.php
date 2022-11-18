<?php

namespace App\Tests\functional\Infrastructure\Doctrine\Service\Profile;

use App\Application\Http\Web\Profile\Dto\CrudProfileDto;
use App\Domain\Doctrine\Identity\Entity\User;
use App\Infrastructure\Doctrine\Service\Profile\ProfileCrudService;
use App\Tests\FunctionalTester;
use Exception;

class ProfileCrudServiceCest
{
    /**
     * @param FunctionalTester $I
     * @return void
     * @throws Exception
     */
    public function update(FunctionalTester $I): void
    {
        $dto = new CrudProfileDto();

        $login = $dto->login = $I->faker()->word();
        $email = $dto->email = $I->faker()->email();
        $telegramId = $dto->telegramId = random_int(10000000, 99999999);

        $user = $this->getUser($I);

        $this->getService($I)->update($user, $dto);

        $I->seeInRepository(User::class, [
            'id' => $user->getIdToString(),
            'name' => $login,
            'email' => $email,
            'telegramId' => $telegramId,
        ]);
    }

    /**
     * @param FunctionalTester $I
     * @return void
     * @throws Exception
     */
    public function updateWithPassword(FunctionalTester $I): void
    {
        $dto = new CrudProfileDto();

        $login = $dto->login = $I->faker()->word();
        $email = $dto->email = $I->faker()->email();
        $telegramId = $dto->telegramId = random_int(10000000, 99999999);
        $dto->isChangePassword = 'on';
        $dto->password = $dto->passwordAgan = $I->faker()->word();

        $user = $this->getUser($I);

        $oldPassword = $user->getPassword();

        $this->getService($I)->update($user, $dto);

        $I->seeInRepository(User::class, [
            'id' => $user->getIdToString(),
            'name' => $login,
            'email' => $email,
            'telegramId' => $telegramId,
        ]);

        $I->dontSeeInRepository(User::class, [
            'id' => $user->getIdToString(),
            'password' => $oldPassword,
        ]);
    }

    private function getService(FunctionalTester $I): ProfileCrudService
    {
        return $I->grabService(ProfileCrudService::class);
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