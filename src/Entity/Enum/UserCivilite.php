<?php

namespace App\Entity\Enum;

enum UserCivilite: string
{
    case MADAME = 'madame';
    case MONSIEUR = 'monsieur';
    case AUTRE = 'autre';
    case INCONNU = 'inconnu';

    public function label(): string
    {
        return match ($this) {
            self::MADAME => 'Madame',
            self::MONSIEUR => 'Monsieur',
            self::AUTRE => 'Autre',
            self::INCONNU => 'Inconnu',
        };
    }
}