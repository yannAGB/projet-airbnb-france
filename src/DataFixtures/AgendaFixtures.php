<?php

namespace App\DataFixtures;

use App\Entity\Agenda;
use App\Entity\Enum\AgendaStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AgendaFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['agendas'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {

            $agenda = new Agenda();

            $arrival   = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 months', '+3 months')
            );
            $departure = $arrival->modify('+' . rand(2, 15) . ' days');

            $agenda->setDateArrivee($arrival);
            $agenda->setDateDepart($departure);
            $agenda->setNote($faker->sentence());
            $agenda->setStatus($faker->randomElement(AgendaStatus::cases()));

            $manager->persist($agenda);

            /* Référence utilisable par d'autres fixtures */
            $this->addReference('agenda-' . $i, $agenda);
        }

        $manager->flush();
    }
}