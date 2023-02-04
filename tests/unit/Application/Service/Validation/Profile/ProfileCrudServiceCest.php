<?php

namespace App\Tests\unit\Application\Service\Validation\Profile;

use App\Application\Http\Web\Profile\Dto\CrudProfileDto;
use App\Domain\Event\AlertNotificationEvent;
use App\Domain\Identity\Entity\User;
use App\Domain\Notification\Service\NotifyService;
use App\Domain\Profile\Factory\ProfileCrudFactory;
use App\Domain\Profile\Service\ProfileCrudService;
use App\Domain\VisualNotification\Service\VisualNotificationService;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
use App\Infrastructure\Event\Listener\AlertNotificationEventListener;
use App\Tests\UnitTester;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileCrudServiceCest
{
    public function positiveCreateNotifyEvent(UnitTester $I): void
    {
        $user = $this->getUser($I);

        $dto = new CrudProfileDto();

        $dto->login = $I->faker()->word();
        $dto->email = $I->faker()->email();
        $dto->telegramId = random_int(10000000, 99999999);
        $dto->isChangePassword = null;
        $dto->password = $dto->passwordAgan = $I->faker()->word();

        $service = new ProfileCrudService(
            crud: $I->grabService(ProfileCrudFactory::class),
            passwordHasher: Stub::makeEmpty(UserPasswordHasherInterface::class),
            eventDispatcher: Stub::makeEmpty(
                class: EventDispatcherInterface::class,
                params: [
                    'dispatch' => Expected::exactly(1)
                ]
            )
        );

        $service->update($user, $dto);
    }

    public function checkEventListener(UnitTester $I): void
    {
        $eventDispatcher = Stub::makeEmpty(
            EventDispatcherInterface::class,
            ['dispatch' => Expected::exactly(1, fn () => (object)[])]
        );

        $service = new NotifyService(
            eventDispatcher: $eventDispatcher,
            service: $I->grabService(VisualNotificationService::class),
            repository: $I->grabService(UserRepository::class)
        );

        $dispatcher = new EventDispatcher();

        $listener = new AlertNotificationEventListener($service);
        $dispatcher->addListener('notification.alert.send', [$listener, 'onAlertSend']);

        $event = new AlertNotificationEvent(
            message: $message = $I->faker()->text(),
            types: $types = [AlertNotificationEvent::MESSENGER]
        );
        $dispatcher->dispatch($event, AlertNotificationEvent::NAME);

        $I->assertEquals($message, $event->getMessage());
        $I->assertEquals($types, $event->getTypes());
    }

    private function getUser(UnitTester $I): User
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