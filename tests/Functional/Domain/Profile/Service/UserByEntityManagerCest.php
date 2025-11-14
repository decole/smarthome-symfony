<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\Profile\Service;

use App\Domain\Identity\Entity\User;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\Skip;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Rfc4122\UuidV4;

#[Skip('This test not support new version codeception')]
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

        $userManager->find(User::class, (string) $id);

        $I->seeInRepository(User::class, [
            'id' => (string) $id,
            'name' => $login,
            'email' => $email,
            'telegramId' => $telegramId,
        ]);
    }
}
