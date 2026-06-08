<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\BookingService;
use App\Repository\RealEstateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_booking_')]
final class ApiBookingController extends AbstractController
{
    public function __construct(
        private BookingService $bookingService,
		private RealEstateRepository $realEstateRepository,
    ) {}

    /* -------------------------------------------------- */
    /*         GET /api/bookings                          */
    /* -------------------------------------------------- */
    #[Route('/bookings', name: 'list', methods: ['GET'])]
    public function index(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Non authentifié'],
                Response::HTTP_UNAUTHORIZED);
        }

        $bookings = $this->bookingService->getReservationsByHost($user);

        return $this->json([
            'success' => true,
            'total'   => count($bookings),
            'data'    => $this->bookingService->serialiserListe($bookings),
        ], Response::HTTP_OK);
    }

    /* -------------------------------------------------- */
    /*         GET /api/bookings/upcoming                 */
    /* -------------------------------------------------- */
    #[Route('/bookings/upcoming', name: 'upcoming', methods: ['GET'])]
    public function upcoming(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Non authentifié'],
                Response::HTTP_UNAUTHORIZED);
        }

        $limit    = $request->query->getInt('limit', 5);
        $bookings = $this->bookingService->getUpcomingByHost($user, $limit);

        return $this->json([
            'success' => true,
            'total'   => count($bookings),
            'data'    => $this->bookingService->serialiserListe($bookings),
        ], Response::HTTP_OK);
    }

    /* -------------------------------------------------- */
    /*         PATCH /api/bookings/{id}/statut            */
    /* -------------------------------------------------- */
    #[Route('/bookings/{id}/statut', name: 'update_statut', methods: ['PATCH'])]
    public function updateStatut(
        int     $id,
        Request $request,
        #[CurrentUser] ?User $user
    ): JsonResponse
    {
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Non authentifié'],
                Response::HTTP_UNAUTHORIZED);
        }

        $donnees = json_decode($request->getContent(), true);
        $statut  = $donnees['statut'] ?? null;

        if (!$statut) {
            return $this->json(['success' => false, 'message' => 'Statut manquant'],
                Response::HTTP_BAD_REQUEST);
        }

        $booking = $this->bookingService->updateStatut($id, $statut, $user);

        if (!$booking) {
            return $this->json(['success' => false, 'message' => 'Réservation introuvable ou accès refusé'],
                Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data'    => $this->bookingService->serialiser($booking),
        ], Response::HTTP_OK);
    }

	/* -------------------------------------------------- */
	/*         POST /api/bookings/create                  */
	/* -------------------------------------------------- */
	#[Route('/bookings/create', name: 'create', methods: ['POST'])]
	public function create(
		Request $request,
		#[CurrentUser] ?User $user
	): JsonResponse
	{
		if (!$user) {
			return $this->json(['success' => false, 'message' => 'Non authentifié'],
				Response::HTTP_UNAUTHORIZED);
		}

		$donnees = json_decode($request->getContent(), true);

		$champsObligatoires = ['realEstateId', 'dateArrivee', 'dateDepart', 'nbVoyageurs'];
		foreach ($champsObligatoires as $champ) {
			if (empty($donnees[$champ])) {
				return $this->json([
					'success' => false,
					'message' => "Le champ « $champ » est obligatoire",
				], Response::HTTP_BAD_REQUEST);
			}
		}

		$re = $this->realEstateRepository->find($donnees['realEstateId']);

		if (!$re) {
			return $this->json(['success' => false, 'message' => 'Logement introuvable'],
				Response::HTTP_NOT_FOUND);
		}

		/* Empêcher le propriétaire de réserver son propre logement */
		if ($re->getOwner()?->getId() === $user->getId()) {
			return $this->json([
				'success' => false,
				'message' => 'Vous ne pouvez pas réserver votre propre logement',
			], Response::HTTP_FORBIDDEN);
		}

		try {
			$arrivee = new \DateTimeImmutable($donnees['dateArrivee']);
			$depart  = new \DateTimeImmutable($donnees['dateDepart']);
			$nuits   = (int) $arrivee->diff($depart)->days;

			$booking = $this->bookingService->creerReservation(
				guest       : $user,
				realEstate  : $re,
				dateArrivee : $arrivee,
				dateDepart  : $depart,
				nbNuits     : $nuits,
				nbVoyageurs : (int) $donnees['nbVoyageurs'],
				montant     : $re->getPrice() * $nuits,
				note        : $donnees['note'] ?? null,
			);
		} catch (\Exception $e) {
			return $this->json([
				'success' => false,
				'message' => 'Erreur lors de la création de la réservation',
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return $this->json([
			'success' => true,
			'message' => 'Réservation créée avec succès',
			'data'    => $this->bookingService->serialiser($booking),
		], Response::HTTP_CREATED);
	}
}