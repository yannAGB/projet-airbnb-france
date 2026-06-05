<?php

namespace App\Service;

use App\Entity\RealEstate;
use App\Repository\RealEstateRepository;
use App\Repository\LikeRepository;
use App\Repository\BookingRepository;

class RealEstateService
{
    public function __construct(
		private RealEstateRepository $realEstateRepository,
  		private LikeRepository       $likeRepository,
    	private BookingRepository    $bookingRepository,
    ) {}

    /* -------------------------------------------------- */
    /*                     Requêtes                       */
    /* -------------------------------------------------- */

    public function getTousLesLogements(): array
    {
        return $this->realEstateRepository->findAllOnline();
    }

    public function getLogementsAccueil(int $limit = 6): array
    {
        return $this->realEstateRepository->findForHome($limit);
    }

    public function getCoupDeCoeur(int $limit = 6): array
    {
        return $this->realEstateRepository->findCoupDeCoeur($limit);
    }

    public function getDestinationsPopulaires(int $limit = 5): array
    {
        return $this->realEstateRepository->findDestinationsPopulaires($limit);
    }

    public function trouverParId(int $id): ?RealEstate
    {
        return $this->realEstateRepository->find($id);
    }

    public function trouverParSlug(string $slug): ?RealEstate
    {
        return $this->realEstateRepository->findOneBySlug($slug);
    }

    public function trouverParCategorie(string $categorieSlug): array
    {
        return $this->realEstateRepository->findByCategorie($categorieSlug);
    }

    public function countOnline(): int
    {
        return $this->realEstateRepository->countOnline();
    }

	/* ---- Avis d'un logement ---- */
	public function getReviews(RealEstate $re): array
	{
		return $this->likeRepository->findByRealEstate($re);
	}

	public function getNoteMoyenne(RealEstate $re): float
	{
		return $this->likeRepository->getNoteMoyenne($re);
	}

	/* ---- Disponibilités (dates réservées) ---- */
	public function getDisponibilites(RealEstate $re): array
	{
		$bookings = $this->bookingRepository->findByRealEstate($re);

		return array_map(fn($b) => [
			'dateArrivee' => $b->getDateArrivee()?->format('Y-m-d'),
			'dateDepart'  => $b->getDateDepart() ?->format('Y-m-d'),
			'statut'      => $b->getStatut()->value,
		], $bookings);
	}


    /* -------------------------------------------------- */
    /*               Sérialisation JSON                   */
    /* -------------------------------------------------- */

/* ---- Sérialisation étendue avec reviews et amenities ---- */
public function serialiserDetail(RealEstate $re): array
{
    $base    = $this->serialiserDetail($re);
    $owner   = $re->getOwner();
    $reviews = $this->getReviews($re);

    $initiales = '';
    if ($owner) {
        $initiales = strtoupper(
            substr($owner->getFirstName() ?? '', 0, 1) .
            substr($owner->getLastName()  ?? '', 0, 1)
        );
    }

    return array_merge($base, [
        'amenities'    => $re->getAmenities() ?? [],
        'note_moyenne' => $this->getNoteMoyenne($re),
        'nb_avis'      => count($reviews),
        'hote'         => $owner ? [
            'id'        => $owner->getId(),
            'firstName' => $owner->getFirstName(),
            'lastName'  => $owner->getLastName(),
            'initiales' => $initiales,
            'created_at'=> $owner->getCreatedAt()?->format('Y'),
        ] : null,
        'reviews'      => array_map(fn($like) => [
            'id'        => $like->getId(),
            'note'      => $like->getReview(),
            'comment'   => $like->getComment(),
            'created_at'=> $like->getCreatedAt()?->format('Y-m-d'),
            'reviewer'  => [
                'firstName' => $like->getReviewer()?->getFirstName() ?? 'Anonyme',
                'lastName'  => $like->getReviewer()?->getLastName()  ?? '',
                'initiales' => strtoupper(
                    substr($like->getReviewer()?->getFirstName() ?? 'A', 0, 1) .
                    substr($like->getReviewer()?->getLastName()  ?? 'N', 0, 1)
                ),
            ],
        ], $reviews),
    ]);
}

    public function serialiserListe(array $logements): array
    {
        return array_map(
            fn($logement) => $this->serialiserDetail($logement),
            $logements
        );
    }
}