<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NotificationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['notifications'];
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {

            $notification = new Notification();

			$notification->setSender($this->getReference('user-' . rand(0, 39), User::class));
            $notification->setTitle($faker->sentence(4));
            $notification->setContent($faker->paragraph());
            $notification->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-6 months')
            ));

            $manager->persist($notification);
        }

        $manager->flush();
    }
}