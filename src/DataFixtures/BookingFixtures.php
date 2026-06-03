<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Enum\BookingStatus;
use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\RealEstate;

class BookingFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array { return ['bookings']; }

    public function getDependencies(): array
    {
        return [UserFixtures::class, RealEstateFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /* Assigner un owner aux RealEstates ---- */
        for ($i = 0; $i < 80; $i++) {
            /** @var RealEstate $re */
            $re    = $this->getReference('real-estate-' . $i, RealEstate::class);
            $owner = $this->getReference('user-' . ($i % 10), User::class);
            $re->setOwner($owner);
            $manager->persist($re);
        }

        /* Créer des réservations ---- */
        $statuts = BookingStatus::cases();

        for ($i = 0; $i < 60; $i++) {
            $booking  = new Booking();
            $guest    = $this->getReference('user-' . rand(10, 39), User::class);
            $re       = $this->getReference('real-estate-' . rand(0, 79), RealEstate::class);
            $arrivee  = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 month', '+3 months')
            );
            $nuits    = rand(1, 14);
            $depart   = $arrivee->modify("+$nuits days");

            $booking->setGuest       ($guest);
            $booking->setRealEstate  ($re);
            $booking->setDateArrivee ($arrivee);
            $booking->setDateDepart  ($depart);
            $booking->setNbNuits     ($nuits);
            $booking->setNbVoyageurs (rand(1, 6));
            $booking->setMontant     ($re->getPrice() * $nuits);
            $booking->setStatut      ($faker->randomElement($statuts));

            if ($faker->boolean(30)) {
                $booking->setNote($faker->sentence());
            }

            $manager->persist($booking);

            /* Créer une notification associée ---- */
            if ($faker->boolean(50)) {
                $notif = new Notification();
                $notif->setSender   ($guest);
                $notif->setRecipient($re->getOwner());
                $notif->setTitle    ('Nouvelle réservation');
                $notif->setContent  ($faker->sentence(8));
                $notif->setCreatedAt(\DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-2 weeks')
                ));
                $manager->persist($notif);
            }
        }

        $manager->flush();
    }
}