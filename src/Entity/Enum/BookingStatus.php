<?php

namespace App\Entity\Enum;

enum BookingStatus: string
{
    case EN_ATTENTE  = 'en_attente';
    case CONFIRME    = 'confirme';
    case A_CONFIRMER = 'a_confirmer';
    case ANNULE      = 'annule';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE  => 'En attente',
            self::CONFIRME    => 'Confirmé',
            self::A_CONFIRMER => 'À confirmer',
            self::ANNULE      => 'Annulé',
        };
    }
}