<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Enum\BookingStatus;
use App\Entity\User;
use App\Entity\RealEstate;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    public function __construct(
        private BookingRepository      $bookingRepository,
        private EntityManagerInterface $em,
    ) {}

    public function getReservationsByHost(User $host): array
    {
        return $this->bookingRepository->findByHost($host);
    }

    public function getUpcomingByHost(User $host, int $limit = 5): array
    {
        return $this->bookingRepository->findUpcomingByHost($host, $limit);
    }

    public function getRevenueMois(User $host): float
    {
        return $this->bookingRepository->getRevenueMoisByHost($host);
    }

    public function countAVenir(User $host): int
    {
        return $this->bookingRepository->countUpcomingByHost($host);
    }

    public function updateStatut(int $id, string $statut, User $host): ?Booking
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) return null;
        if ($booking->getRealEstate()?->getOwner()?->getId() !== $host->getId()) return null;

        $booking->setStatut(BookingStatus::from($statut));
        $this->em->flush();

        return $booking;
    }

    /* ---- Sérialisation ---- */
    public function serialiser(Booking $booking): array
    {
        $re    = $booking->getRealEstate();
        $guest = $booking->getGuest();

        $image = null;
        if ($re && !$re->getImages()->isEmpty()) {
            $image = $re->getImages()->first()->getName();
        }

        $initiales = strtoupper(
            substr($guest?->getFirstName() ?? '?', 0, 1) .
            substr($guest?->getLastName()  ?? '?', 0, 1)
        );

        return [
            'id'          => $booking->getId(),
            'guest'       => [
                'id'        => $guest?->getId(),
                'firstName' => $guest?->getFirstName(),
                'lastName'  => $guest?->getLastName(),
                'email'     => $guest?->getEmail(),
                'initiales' => $initiales,
            ],
            'logement'    => [
                'id'    => $re?->getId(),
                'title' => $re?->getTitle(),
                'slug'  => $re?->getSlug(),
                'image' => $image,
            ],
            'dateArrivee' => $booking->getDateArrivee()?->format('d M Y'),
            'dateDepart'  => $booking->getDateDepart()?->format('d M Y'),
            'nbNuits'     => $booking->getNbNuits(),
            'nbVoyageurs' => $booking->getNbVoyageurs(),
            'montant'     => $booking->getMontant(),
            'statut'      => $booking->getStatut()->value,
            'note'        => $booking->getNote(),
            'created_at'  => $booking->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public function serialiserListe(array $bookings): array
    {
        return array_map(fn($b) => $this->serialiser($b), $bookings);
    }

	public function creerReservation(
		User             $guest,
		RealEstate       $realEstate,
		\DateTimeImmutable $dateArrivee,
		\DateTimeImmutable $dateDepart,
		int              $nbNuits,
		int              $nbVoyageurs,
		float            $montant,
		?string          $note = null,
	): Booking
	{
		$booking = new Booking();
		$booking->setGuest      ($guest);
		$booking->setRealEstate ($realEstate);
		$booking->setDateArrivee($dateArrivee);
		$booking->setDateDepart ($dateDepart);
		$booking->setNbNuits    ($nbNuits);
		$booking->setNbVoyageurs($nbVoyageurs);
		$booking->setMontant    ($montant);
		$booking->setStatut     (BookingStatus::EN_ATTENTE);
		$booking->setNote       ($note);

		$this->em->persist($booking);
		$this->em->flush();

		return $booking;
	}

}