<?php

namespace App\DataFixtures;

use App\Entity\Like;
use App\Entity\RealEstate;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LikeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public static function getGroups(): array { return ['likes']; }

	public function getDependencies(): array
    {
        return [UserFixtures::class, RealEstateFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 150; $i++) {

            $like = new Like();

            $like->setReview(rand(3, 5));
            $like->setComment($faker->boolean(70) ? $faker->sentence(12) : null);
			$like->setRealEstate($this->getReference('real-estate-' . rand(0, 79), RealEstate::class));
			$like->setReviewer($this->getReference('user-' . rand(0, 39), User::class));
            $like->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year')
            ));

            $manager->persist($like);
        }

        $manager->flush();
    }
}