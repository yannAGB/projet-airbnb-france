<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{
    TextField,
    TextEditorField,
    DateTimeField,
    AssociationField,
    FormField
};

class CategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            FormField::addTab('Catégorie'),

            TextField::new('title'),
            TextField::new('slug'),

            TextEditorField::new('description'),

            AssociationField::new('parent')
                ->setFormTypeOption('choice_label', 'title'),

            /* FormField::addTab('Dates'), */

			DateTimeField::new('created_at')
				->hideOnForm(),

			DateTimeField::new('updated_at')
				->hideOnForm(),
        ];
    }
}