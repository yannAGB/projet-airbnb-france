<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Enum\UserCivilite;
use App\Entity\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserPasswordHasherInterface $hasher,
        private SluggerInterface            $slugger,
        private UserRepository              $userRepository,
    ) {}

    /* -------------------------------------------------- */
    /*                    Validation                      */
    /* -------------------------------------------------- */
    public function validerDonnees(?array $donnees): array
    {
        if (!$donnees) {
            return [
                'success' => false,
                'message' => 'Corps de la requête invalide ou vide',
            ];
        }

        $champsObligatoires = [
            'lastName', 'firstName', 'username',
            'email', 'password', 'birthday', 'civilite'
        ];

        foreach ($champsObligatoires as $champ) {
            if (empty($donnees[$champ])) {
                return [
                    'success' => false,
                    'message' => "Le champ « $champ » est obligatoire",
                ];
            }
        }

        if (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Format d\'email invalide',
            ];
        }

        if (strlen($donnees['password']) < 8) {
            return [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 8 caractères',
            ];
        }

        return ['success' => true];
    }

    /* -------------------------------------------------- */
    /*               Vérifications unicité                */
    /* -------------------------------------------------- */
    public function emailExiste(string $email): bool
    {
        return (bool) $this->userRepository->findOneBy(['email' => $email]);
    }

    public function usernameExiste(string $username): bool
    {
        return (bool) $this->userRepository->findOneBy(['username' => $username]);
    }

    /* -------------------------------------------------- */
    /*               Création utilisateur                 */
    /* -------------------------------------------------- */
    public function creerUtilisateur(array $donnees): User
    {
        $user = new User();

        $user->setLastName ($donnees['lastName'] );
        $user->setFirstName($donnees['firstName']);
        $user->setUsername ($donnees['username'] );
        $user->setEmail    ($donnees['email']    );
        $user->setPassword(
            $this->hasher->hashPassword($user, $donnees['password'])
        );
        $user->setBirthday(
            new \DateTimeImmutable($donnees['birthday'])
        );
        $user->setCivilite(
            UserCivilite::from($donnees['civilite'])
        );
        $user->setRoles   ([$donnees['role'] ?? 'ROLE_USER']);
        $user->setSlug(
            strtolower($this->slugger->slug(
                $donnees['firstName'] . '-' . $donnees['lastName']
            ))
        );
        $user->setStatus    (UserStatus::VALID    );
        $user->setIsValid   (true                 );
        $user->setIsValid(false                );
        $user->setCreatedAt (new \DateTimeImmutable());
        $user->setUpdatedAt (new \DateTimeImmutable());
        $user->setLastLogin (new \DateTimeImmutable());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /* -------------------------------------------------- */
    /*                     Requêtes                       */
    /* -------------------------------------------------- */
    public function getTousLesUtilisateurs(): array
    {
        return $this->userRepository->findAll();
    }

    public function trouverParId(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /* -------------------------------------------------- */
    /*               Sérialisation JSON                   */
    /* -------------------------------------------------- */
    public function serialiser(User $user): array
    {
        return [
            'id'          => $user->getId(),
            'lastName'    => $user->getLastName(),
            'firstName'   => $user->getFirstName(),
            'username'    => $user->getUsername(),
            'email'       => $user->getEmail(),
            'slug'        => $user->getSlug(),
            'roles'       => $user->getRoles(),
            'civilite'    => $user->getCivilite()?->value,
            'status'      => $user->getStatus()?->value,
            'is_valid'    => $user->isValid(),
            'is_verified' => $user->isValid(),
            'birthday'    => $user->getBirthday()?->format('Y-m-d'),
            'created_at'  => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at'  => $user->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'last_login'  => $user->getLastLogin()?->format('Y-m-d H:i:s'),
        ];
    }
}