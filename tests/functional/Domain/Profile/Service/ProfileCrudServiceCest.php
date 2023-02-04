<?php

namespace App\Tests\functional\Domain\Profile\Service;

use App\Application\Http\Web\Profile\Dto\CrudProfileDto;
use App\Domain\Identity\Entity\User;
use App\Domain\Profile\Factory\ProfileCrudFactory;
use App\Domain\Profile\Service\ProfileCrudService;
use App\Tests\FunctionalTester;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileCrudServiceCest
{
    /**
     * @param FunctionalTester $I
     * @return void
     * @throws Exception
     */
    public function update(FunctionalTester $I): void
    {
        $eventDispatcher = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => Expected::never()]
        );

        $dto = new CrudProfileDto();

        $login = $dto->login = $I->faker()->word();
        $email = $dto->email = $I->faker()->email();
        $telegramId = $dto->telegramId = random_int(10000000, 99999999);

        $user = $this->getUser($I);

        $this->getService($I, $eventDispatcher)->update($user, $dto);

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
        $eventDispatcher = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => Expected::exactly(1, fn () => (object)[])]
        );

        $dto = new CrudProfileDto();

        $login = $dto->login = $I->faker()->word();
        $email = $dto->email = $I->faker()->email();
        $telegramId = $dto->telegramId = random_int(10000000, 99999999);
        $dto->isChangePassword = 'on';
        $dto->password = $dto->passwordAgan = $I->faker()->word();

        $user = $this->getUser($I);

        $oldPassword = $user->getPassword();

        $this->getService($I, $eventDispatcher)->update($user, $dto);

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

    private function getService(FunctionalTester $I, EventDispatcherInterface $eventDispatcher): ProfileCrudService
    {
        return new ProfileCrudService(
            crud: $I->grabService(ProfileCrudFactory::class),
            passwordHasher: $I->grabService(UserPasswordHasherInterface::class),
            eventDispatcher: $eventDispatcher
        );
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