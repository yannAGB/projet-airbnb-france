<?php

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Entity\User;
use App\Entity\Enum\PaymentStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PaymentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['payments'];
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {

            $payment = new Payment();

            $amount = rand(50, 3000);
            $taxe   = $amount * 0.2;
            $total  = $amount + $taxe;

            $payment->setIdStripe('pi_' . bin2hex(random_bytes(12)));
            $payment->setUser($this->getReference('user-' . rand(0, 39), User::class));
            $payment->setAmount($amount);
            $payment->setTaxe($taxe);
            $payment->setTotal($total);
            $payment->setStatus($faker->randomElement(PaymentStatus::cases()));
            $payment->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year')
            ));

            $manager->persist($payment);
        }

        $manager->flush();
    }
}