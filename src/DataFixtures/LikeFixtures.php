<?php

namespace App\DataFixtures;

use App\Entity\Like;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LikeFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['likes'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 150; $i++) {

            $like = new Like();

            $like->setReview(rand(1, 500));
            $like->setComment($faker->optional()->sentence());
            $like->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year')
            ));

            $manager->persist($like);
        }

        $manager->flush();
    }
}