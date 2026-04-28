<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Agenda;
use App\Entity\Payment;
use App\Entity\Image;
use App\Entity\Like;
use App\Entity\Categorie;
use App\Entity\Notification;
use App\Entity\RealEstate;

use App\Entity\Enum\UserRole;
use App\Entity\Enum\UserStatus;
use App\Entity\Enum\UserCivilite;
use App\Entity\Enum\PaymentStatus;
use App\Entity\Enum\AgendaStatus;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private SluggerInterface $slugger
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        */

        $categories = [];

        $categoriesData = [
            'Appartement',
            'Villa',
            'Maison',
            'Studio',
            'Loft',
            'Cabane',
            'Château',
            'Bungalow'
        ];

        foreach ($categoriesData as $catTitle) {

            $category = new Categorie();

            $category->setTitle($catTitle);
            $category->setSlug(
                strtolower($this->slugger->slug($catTitle))
            );

            $category->setDescription(
                $faker->paragraph()
            );

            $category->setCreatedAt(
                new \DateTimeImmutable()
            );

            $category->setUpdatedAt(
                new \DateTimeImmutable()
            );

            $manager->persist($category);

            $categories[] = $category;
        }

        /*
        |--------------------------------------------------------------------------
        | AGENDAS
        |--------------------------------------------------------------------------
        */

        $agendas = [];

        for ($i = 0; $i < 30; $i++) {

            $agenda = new Agenda();

            $arrival = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 months', '+3 months')
            );

            $departure = $arrival->modify('+'.rand(2, 15).' days');

            $agenda->setDateArrivee($arrival);
            $agenda->setDateDepart($departure);

            $agenda->setNote(
                $faker->sentence()
            );

            $agenda->setStatus(
                $faker->randomElement(AgendaStatus::cases())
            );

            $manager->persist($agenda);

            $agendas[] = $agenda;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        $admin = new User();

        $admin->setName('Administrateur');
        $admin->setUsername('admin');
        $admin->setEmail('admin@airbnbclone.com');

        $admin->setPassword(
            $this->hasher->hashPassword(
                $admin,
                'password'
            )
        );

		$admin->setRoles([
			UserRole::ADMIN->value
		]);

        $admin->setBirthday(
            new \DateTimeImmutable('1990-01-01')
        );

        $admin->setSlug('administrateur');

        $admin->setCivilite(UserCivilite::MONSIEUR);

        $admin->setCreatedAt(
            new \DateTimeImmutable()
        );

        $admin->setUpdatedAt(
            new \DateTimeImmutable()
        );

        $admin->setLastLogin(
            new \DateTimeImmutable()
        );

        $admin->setIsValid(true);

        $admin->setStatus(
            UserStatus::VALID
        );

        $admin->setAgenda(
            $faker->randomElement($agendas)
        );

        $manager->persist($admin);

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        $users = [];

        for ($i = 0; $i < 40; $i++) {

            $user = new User();

            $fullname = $faker->name();

            $user->setName($fullname);

            $user->setUsername(
                strtolower(
                    $faker->userName()
                )
            );

            $user->setEmail(
                $faker->unique()->safeEmail()
            );

            $user->setPassword(
                $this->hasher->hashPassword(
                    $user,
                    'password'
                )
            );

			$user->setRoles([
				$faker->randomElement([
					UserRole::USER->value,
					UserRole::IMMOBILIER->value
				])
			]);

            $user->setBirthday(
                \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-60 years', '-18 years')
                )
            );

            $user->setSlug(
                strtolower(
                    $this->slugger->slug($fullname)
                )
            );

            $user->setCivilite(
                $faker->randomElement(
                    UserCivilite::cases()
                )
            );

            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year')
            );

            $user->setCreatedAt($createdAt);

            $user->setUpdatedAt(
                $createdAt->modify('+'.rand(1, 30).' days')
            );

            $user->setLastLogin(
                \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-1 month')
                )
            );

            $user->setIsValid(
                $faker->boolean(90)
            );

            $user->setStatus(
                $faker->randomElement(
                    UserStatus::cases()
                )
            );

            $user->setAgenda(
                $faker->randomElement($agendas)
            );

            $manager->persist($user);

            $users[] = $user;
        }

        /*
        |--------------------------------------------------------------------------
        | REAL ESTATES
        |--------------------------------------------------------------------------
        */

        $realEstates = [];

        for ($i = 0; $i < 80; $i++) {

            $realEstate = new RealEstate();

            $title = $faker->randomElement([
                'Magnifique villa avec piscine',
                'Appartement moderne centre-ville',
                'Studio cosy proche plage',
                'Loft luxueux vue mer',
                'Maison familiale calme',
                'Cabane romantique forêt',
            ]);

            $realEstate->setTitle($title);

            $realEstate->setDescription(
                $faker->paragraphs(4, true)
            );

            $realEstate->setPromotion(
                rand(5, 30).'% OFF'
            );

            $realEstate->setSlug(
                strtolower(
                    $this->slugger->slug(
                        $title.'-'.$i
                    )
                )
            );

            $realEstate->setPrice(
                rand(50, 1500)
            );

            $realEstate->setMaxTravelers(
                rand(1, 10)
            );

            $realEstate->setAdults(
                rand(1, 6)
            );

            $realEstate->setChildren(
                rand(0, 4)
            );

            $realEstate->setBabies(
                rand(0, 2)
            );

            $realEstate->setLikes(
                rand(0, 500)
            );

            $realEstate->setStreetNumber(
                (string) rand(1, 300)
            );

            $realEstate->setStreetName(
                $faker->streetName()
            );

            $realEstate->setPostalCode(
                $faker->postcode()
            );

            $realEstate->setAddressLine2(
                $faker->optional()->secondaryAddress()
            );

            $realEstate->setCity(
                $faker->city()
            );

            $realEstate->setCountry('France');

            $realEstate->setLongitude(
                $faker->longitude()
            );

            $realEstate->setLatitude(
                $faker->latitude()
            );

            $realEstate->setIsOnline(
                $faker->boolean(95)
            );

            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-2 years')
            );

            $realEstate->setCreatedAt($createdAt);

            $realEstate->setUpdatedAt(
                $createdAt->modify('+'.rand(1, 100).' days')
            );

            $realEstate->setAgenda(
                $faker->randomElement($agendas)
            );

            $realEstate->setCategorie(
                $faker->randomElement($categories)
            );

            $manager->persist($realEstate);

            $realEstates[] = $realEstate;

            /*
            |--------------------------------------------------------------------------
            | IMAGES
            |--------------------------------------------------------------------------
            */

            for ($j = 0; $j < rand(3, 8); $j++) {

                $image = new Image();

                $image->setRealEstate($realEstate);

                $image->setName(
                    'https://picsum.photos/1200/800?random='.rand(1,99999)
                );

                $image->setCreatedAt(
                    new \DateTimeImmutable()
                );

                $image->setUpdatedAt(
                    new \DateTimeImmutable()
                );

                $manager->persist($image);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENTS
        |--------------------------------------------------------------------------
        */

        for ($i = 0; $i < 100; $i++) {

            $payment = new Payment();

            $amount = rand(50, 3000);
            $taxe = $amount * 0.2;
            $total = $amount + $taxe;

            $payment->setIdStripe(
                'pi_'.bin2hex(random_bytes(12))
            );

            $payment->setIdUser(
                $faker->randomElement($users)
            );

            $payment->setAmount($amount);

            $payment->setTaxe($taxe);

            $payment->setTotal($total);

            $payment->setStatus(
                $faker->randomElement(
                    PaymentStatus::cases()
                )
            );

            $payment->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-1 year')
                )
            );

            $manager->persist($payment);
        }

        /*
        |--------------------------------------------------------------------------
        | NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        for ($i = 0; $i < 100; $i++) {

            $notification = new Notification();

            $notification->setSender(
                $faker->randomElement($users)
            );

            $notification->setTitle(
                $faker->sentence(4)
            );

            $notification->setContent(
                $faker->paragraph()
            );

            $notification->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-6 months')
                )
            );

            $manager->persist($notification);
        }

        /*
        |--------------------------------------------------------------------------
        | LIKES
        |--------------------------------------------------------------------------
        */

        for ($i = 0; $i < 150; $i++) {

            $like = new Like();

            $like->setReview(
                rand(1, 500)
            );

            $like->setComment(
                $faker->optional()->sentence()
            );

            $like->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-1 year')
                )
            );

            $manager->persist($like);
        }

        $manager->flush();
    }
}