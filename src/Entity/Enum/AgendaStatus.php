<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum AgendaStatus: string
{
	case PENDING = 'pending';
	case CONFIRMED = 'confirmed';
	case IN_PROGRESS = 'in_progress';
	case COMPLETED = 'completed';
	case CANCELLED = 'cancelled';
	case RESCHEDULED = 'rescheduled';
	case NO_SHOW = 'no_show';

	public function label(): string
	{
		return match ($this) {
			self::PENDING => 'En attente',
			self::CONFIRMED => 'Confirmé',
			self::IN_PROGRESS => 'En cours',
			self::COMPLETED => 'Terminé',
			self::CANCELLED => 'Annulé',
			self::RESCHEDULED => 'Reprogrammé',
			self::NO_SHOW => 'Absent',
		};
	}
}
