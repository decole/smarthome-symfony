<?php

namespace App\Tests\functional\Domain\Profile\Service;

use App\Domain\Identity\Entity\User;
use App\Tests\FunctionalTester;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Rfc4122\UuidV4;

class UserByEntityManagerCest
{
    public function findUser(FunctionalTester $I): void
    {
        $user = new User();

        $userParams = [
            'isVerified' => true,
            'name' => $login = $I->faker()->name(),
            'email' => $email = $I->faker()->email(),
            'password' => $I->faker()->sha1(),
            'telegramId' => $telegramId = 123412123,
        ];

        /** @var UuidV4 $id */
        $id = $I->haveInRepository($user, $userParams);

        /** @var EntityManager $userManager */
        $userManager = $I->grabService(EntityManager::class);

        $actualUser = $userManager->find(User::class, (string)$id);

        $I->seeInRepository(User::class, [
            'id' => (string)$id,
            'name' => $login,
            'email' => $email,
            'telegramId' => $telegramId,
        ]);
    }
}