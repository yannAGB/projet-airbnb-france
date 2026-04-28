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
}
