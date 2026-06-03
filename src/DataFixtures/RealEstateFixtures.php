<?php

namespace App\DataFixtures;


use App\Entity\Agenda;
use App\Entity\Categorie;
use App\Entity\RealEstate;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class RealEstateFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(
        private SluggerInterface $slugger
    ) {}

    public static function getGroups(): array
    {
        return ['real-estates'];
    }

    public function getDependencies(): array
    {
        return [
            AgendaFixtures::class,
            CategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $titres = [
            'Magnifique villa avec piscine',
            'Appartement moderne centre-ville',
            'Studio cosy proche plage',
            'Loft luxueux vue mer',
            'Maison familiale calme',
            'Cabane romantique forêt',
        ];

        for ($i = 0; $i < 80; $i++) {

            $realEstate = new RealEstate();
            $title      = $faker->randomElement($titres);

            $realEstate->setTitle($title);
            $realEstate->setDescription($faker->paragraphs(4, true));
            $realEstate->setPromotion(rand(5, 30) . '% OFF');
            $realEstate->setSlug(strtolower($this->slugger->slug($title . '-' . $i)));
            $realEstate->setPrice(rand(50, 1500));
            $realEstate->setMaxTravelers(rand(1, 10));
            $realEstate->setAdults(rand(1, 6));
            $realEstate->setChildren(rand(0, 4));
            $realEstate->setBabies(rand(0, 2));
            $realEstate->setLikes(rand(0, 500));
            $realEstate->setStreetNumber((string) rand(1, 300));
            $realEstate->setStreetName($faker->streetName());
            $realEstate->setPostalCode($faker->postcode());
            $realEstate->setAddressLine2($faker->optional()->secondaryAddress());
            $realEstate->setCity($faker->city());
            $realEstate->setCountry('France');
            $realEstate->setLongitude($faker->longitude());
            $realEstate->setLatitude($faker->latitude());
            $realEstate->setIsOnline($faker->boolean(95));
			$realEstate->setIsCoupDeCoeur($i < 10);           
			$realEstate->setIsDestinationPopulaire($i < 5);

            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-2 years')
            );

            $realEstate->setCreatedAt($createdAt);
            $realEstate->setUpdatedAt($createdAt->modify('+' . rand(1, 100) . ' days'));


            $realEstate->setAgenda   ($this->getReference('agenda-'    . rand(0, 29), Agenda::class  ));
            $realEstate->setCategorie($this->getReference('category-' . rand(0, 19), Categorie::class));
			

            $manager->persist($realEstate);
			/* Référence pour les autres fixtures */
			$this->addReference('real-estate-' . $i, $realEstate);

            /* ---- Images liées ---- */
            for ($j = 0; $j < rand(3, 8); $j++) {

                $image = new Image();

                $image->setRealEstate($realEstate);
                $image->setName('https://picsum.photos/1200/800?random=' . rand(1, 99999));
                $image->setCreatedAt(new \DateTimeImmutable());
                $image->setUpdatedAt(new \DateTimeImmutable());

                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}