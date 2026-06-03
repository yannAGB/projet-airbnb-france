<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\LikeRepository;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_dashboard_stats_')]
final class ApiDashboardStatsController extends AbstractController
{
    public function __construct(
        private BookingService $bookingService,
        private LikeRepository $likeRepository,
    ) {}

    /* -------------------------------------------------- */
    /*         GET /api/dashboard/stats                   */
    /* -------------------------------------------------- */
    #[Route('/dashboard/stats', name: 'index', methods: ['GET'])]
    public function index(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Non authentifié'],
                Response::HTTP_UNAUTHORIZED);
        }

        /* Revenus du mois courant */
        $revenusMois = $this->bookingService->getRevenueMois($user);

        /* Réservations à venir */
        $aVenir = $this->bookingService->countAVenir($user);

        /* Note moyenne (sur toutes les reviews Like) */
        $noteMoyenne = $this->getNotemoyenne();
        $nbAvis      = $this->likeRepository->count([]);

        return $this->json([
            'success' => true,
            'data'    => [
                'revenus'     => [
                    'valeur' => $revenusMois,
                    'trend'  => 13,
                ],
                'aVenir'      => [
                    'valeur' => $aVenir,
                    'trend'  => -41,
                ],
                'note'        => [
                    'valeur'  => round($noteMoyenne, 1),
                    'nbAvis'  => $nbAvis,
                ],
            ],
        ], Response::HTTP_OK);
    }

    private function getNotemoyenne(): float
    {
        $result = $this->likeRepository
            ->createQueryBuilder('l')
            ->select('AVG(l.review)')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 4.5);
    }
}