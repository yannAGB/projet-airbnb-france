<?php

namespace App\DataFixtures;

use App\Entity\Agenda;
use App\Entity\User;
use App\Entity\Enum\UserRole;
use App\Entity\Enum\UserStatus;
use App\Entity\Enum\UserCivilite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private SluggerInterface $slugger
    ) {}

    public static function getGroups(): array
    {
        return ['users'];
    }

    public function getDependencies(): array
    {
        return [AgendaFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /* ---- Admin ---- */
        $admin = new User();

        $admin->setName('Administrateur');
        $admin->setUsername('admin');
        $admin->setEmail('admin@airbnbclone.com');
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
        $admin->setRoles([UserRole::ADMIN->value]);
        $admin->setBirthday(new \DateTimeImmutable('1990-01-01'));
        $admin->setSlug('administrateur');
        $admin->setCivilite(UserCivilite::MONSIEUR);
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setUpdatedAt(new \DateTimeImmutable());
        $admin->setLastLogin(new \DateTimeImmutable());
        $admin->setIsValid(true);
        $admin->setStatus(UserStatus::VALID);
        $admin->setAgenda($this->getReference('agenda-' . rand(0, 29), Agenda::class));

        $manager->persist($admin);

        /* ---- Utilisateurs ---- */
        for ($i = 0; $i < 40; $i++) {

            $user     = new User();
            $fullname = $faker->name();

            $user->setName($fullname);
            $user->setUsername(strtolower($faker->userName()));
            $user->setEmail($faker->unique()->safeEmail());
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setRoles([$faker->randomElement([
                UserRole::USER->value,
                UserRole::IMMOBILIER->value
            ])]);
            $user->setBirthday(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-60 years', '-18 years')
            ));
            $user->setSlug(strtolower($this->slugger->slug($fullname)));
            $user->setCivilite($faker->randomElement(UserCivilite::cases()));

            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year')
            );

            $user->setCreatedAt($createdAt);
            $user->setUpdatedAt($createdAt->modify('+' . rand(1, 30) . ' days'));
            $user->setLastLogin(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 month')
            ));
            $user->setIsValid($faker->boolean(90));
            $user->setStatus($faker->randomElement(UserStatus::cases()));
            $user->setAgenda($this->getReference('agenda-' . rand(0, 29), Agenda::class));
			
            $manager->persist($user);

            /* Référence utilisable par d'autres fixtures */
            $this->addReference('user-' . $i, $user);
        }

        $manager->flush();
    }
}