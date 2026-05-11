<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private SluggerInterface $slugger
    ) {}

    public static function getGroups(): array
    {
        return ['categories'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $categoriesData = [
            'Appartement', 'Villa', 'Maison', 'Studio',
            'Loft', 'Cabane', 'Château', 'Bungalow'
        ];

        foreach ($categoriesData as $index => $catTitle) {

            $category = new Categorie();

            $category->setTitle($catTitle);
            $category->setSlug(strtolower($this->slugger->slug($catTitle)));
            $category->setDescription($faker->paragraph());
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($category);

            /* Référence utilisable par d'autres fixtures */
            $this->addReference('category-' . $index, $category);
        }

        $manager->flush();
    }
}