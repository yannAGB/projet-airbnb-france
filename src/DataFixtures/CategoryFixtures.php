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
            'Appartement' => ['Studio', 'T2', 'T3', 'Duplex'],
            'Villa'       => ['Villa avec piscine', 'Villa de luxe'],
            'Maison'      => ['Maison familiale', 'Maison de campagne'],
            'Loft'        => ['Loft industriel', 'Loft moderne'],
            'Cabane'      => ['Cabane dans les arbres', 'Cabane en forêt'],
            'Château'     => [],
            'Bungalow'    => ['Bungalow sur pilotis', 'Bungalow de plage'],
        ];

        $index = 0;

        foreach ($categoriesData as $parentTitle => $sousCategories) {

            /* Catégorie parente */
            $parent = new Categorie();
            $parent->setTitle($parentTitle);
            $parent->setSlug(strtolower($this->slugger->slug($parentTitle)));
            $parent->setDescription($faker->paragraph());
            $parent->setCreatedAt(new \DateTimeImmutable());
            $parent->setUpdatedAt(new \DateTimeImmutable());
            $parent->setParent(null);

            $manager->persist($parent);
            $this->addReference('category-' . $index, $parent);
            $index++;

            /* Sous-catégories */
            foreach ($sousCategories as $sousTitre) {

                $sousCategorie = new Categorie();
                $sousCategorie->setTitle($sousTitre);
                $sousCategorie->setSlug(strtolower($this->slugger->slug($sousTitre)));
                $sousCategorie->setDescription($faker->paragraph());
                $sousCategorie->setCreatedAt(new \DateTimeImmutable());
                $sousCategorie->setUpdatedAt(new \DateTimeImmutable());
                $sousCategorie->setParent($parent);

                $manager->persist($sousCategorie);
                $this->addReference('category-' . $index, $sousCategorie);
                $index++;
            }
        }

        $manager->flush();
    }
}