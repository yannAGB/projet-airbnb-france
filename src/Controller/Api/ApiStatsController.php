<?php

namespace App\Controller\Api;

use App\Repository\RealEstateRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_stats_')]
final class ApiStatsController extends AbstractController
{
    public function __construct(
        private RealEstateRepository $realEstateRepository,
        private UserRepository       $userRepository,
    ) {}

    /* -------------------------------------------------- */
    /*         GET /api/stats                             */
    /* -------------------------------------------------- */
    #[Route('/stats', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data'    => [
                'logements' => $this->realEstateRepository->countOnline(),
                'hotes'     => $this->countHotes(),
            ],
        ], Response::HTTP_OK);
    }

    private function countHotes(): int
    {
        return (int) $this->userRepository
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.is_valid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
}