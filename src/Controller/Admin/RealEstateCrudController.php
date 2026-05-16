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
    CollectionField,
	AssociationField,
    FormField,
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

            // Informations principales
            FormField::addTab('Informations générales'),

            FormField::addColumn(6),
            TextField::new('title'),
            TextEditorField::new('description'),
            TextField::new('promotion'),
			
            FormField::addColumn(6),
			TextField::new('slug'),
            NumberField::new('price'),
            NumberField::new('max_travelers'),
            BooleanField::new('is_online'),

            // Capacité
            FormField::addTab('Capacité'),

            FormField::addColumn(4),
            NumberField::new('adults'),

            FormField::addColumn(4),
            NumberField::new('children'),

            FormField::addColumn(4),
            NumberField::new('babies'),

            // Adresse
            FormField::addTab('Adresse'),

            FormField::addColumn(6),
            TextField::new('streetNumber'),
            TextField::new('streetName'),
            TextField::new('postalCode'),

            FormField::addColumn(6),
            TextField::new('city'),
            TextField::new('country'),
            TextField::new('addressLine2'),

            // Localisation
            FormField::addTab('Géolocalisation'),

            FormField::addColumn(6),
            NumberField::new('latitude'),

            FormField::addColumn(6),
            NumberField::new('longitude'),

            // Relations
            FormField::addTab('Relations'),

            AssociationField::new('categorie')
    			->setFormTypeOption('choice_label', 'title'),

            AssociationField::new('agenda')
				->setFormTypeOption(
					'choice_label',
					'note'
				),
            CollectionField::new('images')
				->onlyOnDetail(),

            // Dates
            /* FormField::addTab('Dates'), */

            DateTimeField::new('created_at')
				->hideOnForm(),
            DateTimeField::new('updated_at')
				->hideOnForm(),
        ];
    }
}