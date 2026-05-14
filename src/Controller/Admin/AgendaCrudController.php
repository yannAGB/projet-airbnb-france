<?php

namespace App\Controller\Admin;

use App\Entity\Agenda;
use App\Entity\Enum\AgendaStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{
    DateTimeField,
    TextField,
    ChoiceField,
    FormField,
	CollectionField
};

class AgendaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agenda::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            FormField::addTab('Planning'),

            DateTimeField::new('date_arrivee'),
            DateTimeField::new('date_depart'),

            TextField::new('note'),

            ChoiceField::new('status')
                ->setChoices([
                    'En attente' => AgendaStatus::PENDING,
                    'Confirmé' => AgendaStatus::CONFIRMED,
                    'En cours' => AgendaStatus::IN_PROGRESS,
                    'Terminé' => AgendaStatus::COMPLETED,
                    'Annulé' => AgendaStatus::CANCELLED,
                ])
				->formatValue(fn ($value) => $value?->value),

            FormField::addTab('Relations'),

			CollectionField::new('userAgenda')
            ->onlyOnDetail(),

			CollectionField::new('realEstate')
				->onlyOnDetail(),
        ];
    }
}