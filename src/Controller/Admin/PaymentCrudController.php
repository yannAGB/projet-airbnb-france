<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Entity\Enum\PaymentStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{
    IdField,
    TextField,
    NumberField,
    DateTimeField,
    AssociationField,
    ChoiceField,
    FormField
};

class PaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Payment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            FormField::addTab('Paiement'),

            TextField::new('id_stripe'),
            AssociationField::new('id_user'),

            NumberField::new('amount'),
            NumberField::new('taxe'),
            NumberField::new('total'),

            ChoiceField::new('status')
                ->setChoices([
                    'En attente' => PaymentStatus::PENDING,
                    'Payé' => PaymentStatus::COMPLETED,
                    'Échoué' => PaymentStatus::FAILED,
                    'Annulé' => PaymentStatus::CANCELLED,
                    'Remboursé' => PaymentStatus::REFUNDED,
                    'Expiré' => PaymentStatus::EXPIRED,
                ]),

            DateTimeField::new('created_at')->hideOnForm(),
        ];
    }
}