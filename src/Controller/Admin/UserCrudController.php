<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Enum\UserCivilite;
use App\Entity\Enum\UserStatus;
use App\Entity\Enum\UserRole;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

use EasyCorp\Bundle\EasyAdminBundle\Field\{
    IdField,
    TextField,
    EmailField,
    DateField,
    ChoiceField,
    BooleanField,
    ArrayField,
    FormField
};

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['created_at' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            /* COLONNE ID (index uniquement, hors tabs) */
            IdField::new('id')->onlyOnIndex(),

            /* ONGLET 1 - Informations personnelles */
            FormField::addTab('👤 Informations personnelles')
                ->onlyOnForms(),

            TextField::new('firstName', 'Prénom')
                ->setColumns(6),

            TextField::new('lastName', 'Nom')
                ->setColumns(6),

            TextField::new('username', 'Nom d\'utilisateur')
                ->setColumns(6),

            EmailField::new('email', 'Adresse email')
                ->setColumns(6),

            DateField::new('birthday', 'Date de naissance')
                ->setColumns(6),

            ChoiceField::new('civilite', 'Civilité')
                ->setChoices([
                    'Madame'   => UserCivilite::MADAME,
                    'Monsieur' => UserCivilite::MONSIEUR,
                    'Autre'    => UserCivilite::AUTRE,
                    'Inconnu'  => UserCivilite::INCONNU,
                ])
                ->renderExpanded(false)
                ->setColumns(6)
                ->formatValue(fn ($value) => $value?->label()),


           /*  ONGLET 2 - Accès & Permissions */
            FormField::addTab('🔐 Accès & Permissions')
                ->onlyOnForms(),

            ArrayField::new('roles', 'Rôles')
                ->onlyOnIndex(),

            ChoiceField::new('roles', 'Rôles')
                ->setChoices([
                    'Admin'       => UserRole::ADMIN->value,
                    'Utilisateur' => UserRole::USER->value,
                    'Agence'      => UserRole::IMMOBILIER->value,
                ])
                ->allowMultipleChoices()
                ->renderExpanded(true)
                ->setColumns(12)
                ->onlyOnForms(),

            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Valide' => UserStatus::VALID,
                    'Banni'  => UserStatus::BANNED,
                ])
                ->renderAsBadges([
                    UserStatus::VALID->value  => 'success',
                    UserStatus::BANNED->value => 'danger',
                ])
                ->formatValue(fn ($value) => $value?->value === 'valid' ? 'Valide' : 'Banni')
                ->setColumns(6),

            BooleanField::new('is_valid', 'Compte actif')
                ->renderAsSwitch(false)
                ->setColumns(6),

            TextField::new('password', 'Mot de passe')
                ->onlyOnForms()
                ->setColumns(6)
                ->setRequired($pageName === Crud::PAGE_NEW),

            /* ONGLET 3 - Historique */
            FormField::addTab('📅 Historique')
                ->onlyOnForms(),

            DateField::new('created_at', 'Créé le')
                ->setColumns(4),

            DateField::new('updated_at', 'Mis à jour le')
                ->setColumns(4),

            DateField::new('last_login', 'Dernière connexion')
                ->setColumns(4),
        ];
    }
}