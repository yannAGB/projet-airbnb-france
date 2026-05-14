<?php

namespace App\Controller\Admin;

use App\Entity\RealEstate;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{
    IdField,
    TextField,
    TextEditorField,
    NumberField,
    BooleanField,
    DateTimeField,
    AssociationField,
    FormField
};

class RealEstateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RealEstate::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            // 🔹 TAB 1 : Infos principales
            FormField::addTab('Informations générales'),

            FormField::addColumn(6),
            TextField::new('title'),
            TextEditorField::new('description'),
            TextField::new('promotion'),

            FormField::addColumn(6),
            NumberField::new('price'),
            NumberField::new('max_travelers'),
            BooleanField::new('is_online'),

            // 🔹 TAB 2 : Capacité
            FormField::addTab('Capacité'),

            FormField::addColumn(4),
            NumberField::new('adults'),

            FormField::addColumn(4),
            NumberField::new('children'),

            FormField::addColumn(4),
            NumberField::new('babies'),

            // 🔹 TAB 3 : Adresse
            FormField::addTab('Adresse'),

            FormField::addColumn(6),
            TextField::new('streetNumber'),
            TextField::new('streetName'),
            TextField::new('postalCode'),

            FormField::addColumn(6),
            TextField::new('city'),
            TextField::new('country'),
            TextField::new('addressLine2'),

            // 🔹 TAB 4 : Localisation
            FormField::addTab('Géolocalisation'),

            FormField::addColumn(6),
            NumberField::new('latitude'),

            FormField::addColumn(6),
            NumberField::new('longitude'),

            // 🔹 TAB 5 : Relations
            FormField::addTab('Relations'),

            AssociationField::new('categorie'),
            AssociationField::new('agenda'),
            AssociationField::new('images'),

            // 🔹 TAB 6 : Dates
            FormField::addTab('Dates'),

            DateTimeField::new('created_at')->hideOnForm(),
            DateTimeField::new('updated_at')->hideOnForm(),
        ];
    }
}