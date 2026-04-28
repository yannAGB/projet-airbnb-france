<?php

namespace App\Entity\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';
    case IMMOBILIER = 'ROLE_AGENCY';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrateur',
            self::USER => 'Utilisateur',
            self::IMMOBILIER => 'Agence immobilière',
        };
    }

	public function redirectRoute(): string
	{
		return match ($this) {
			self::ADMIN => 'admin_dashboard',
			self::USER => 'user_home',
			self::IMMOBILIER => 'agency_dashboard',
		};
	}
}