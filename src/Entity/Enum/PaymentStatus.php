<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum PaymentStatus: string
{
    case PENDING    = 'pending';     
    case COMPLETED  = 'completed';
    case FAILED     = 'failed';
    case CANCELLED  = 'cancelled';
    case REFUNDED   = 'refunded';
    case EXPIRED    = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING   => 'En attente',
            self::COMPLETED => 'Payé',
            self::FAILED    => 'Échoué',
            self::CANCELLED => 'Annulé',
            self::REFUNDED  => 'Remboursé',
            self::EXPIRED   => 'Expiré',
        };
    }
}