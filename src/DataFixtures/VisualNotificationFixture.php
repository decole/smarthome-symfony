<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\VisualNotification\Entity\VisualNotification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class VisualNotificationFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $notify = new VisualNotification(VisualNotification::MESSAGE_TYPE, 'test');

        $manager->persist($notify);

        $manager->flush();
    }
}
