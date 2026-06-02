<?php

namespace App\Controller\Api;

use App\Service\RealEstateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_real_estate_')]
final class ApiRealEstateController extends AbstractController
{
    public function __construct(
        private RealEstateService $realEstateService,
    ) {}

    /* -------------------------------------------------- */
    /*         GET /api/real-estates                      */
    /* -------------------------------------------------- */
    #[Route('/real-estates', name: 'list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $limit     = $request->query->getInt('limit', 0);
        $categorie = $request->query->get('categorie');

        $logements = match(true) {
            !empty($categorie) => $this->realEstateService->trouverParCategorie($categorie),
            $limit > 0         => $this->realEstateService->getLogementsAccueil($limit),
            default            => $this->realEstateService->getTousLesLogements(),
        };

        return $this->json([
            'success' => true,
            'total'   => count($logements),
            'data'    => $this->realEstateService->serialiserListe($logements),
        ], Response::HTTP_OK);
    }

    /* -------------------------------------------------- */
    /*         GET /api/real-estates/coup-de-coeur        */
    /* -------------------------------------------------- */
    #[Route('/real-estates/coup-de-coeur', name: 'coup_de_coeur', methods: ['GET'])]
    public function coupDeCoeur(Request $request): JsonResponse
    {
        $limit    = $request->query->getInt('limit', 6);
        $logements = $this->realEstateService->getCoupDeCoeur($limit);

        return $this->json([
            'success' => true,
            'total'   => count($logements),
            'data'    => $this->realEstateService->serialiserListe($logements),
        ], Response::HTTP_OK);
    }

    /* -------------------------------------------------- */
    /*         GET /api/real-estates/destinations         */
    /* -------------------------------------------------- */
    #[Route('/real-estates/destinations', name: 'destinations', methods: ['GET'])]
    public function destinations(Request $request): JsonResponse
    {
        $limit    = $request->query->getInt('limit', 5);
        $logements = $this->realEstateService->getDestinationsPopulaires($limit);

        return $this->json([
            'success' => true,
            'total'   => count($logements),
            'data'    => $this->realEstateService->serialiserListe($logements),
        ], Response::HTTP_OK);
    }

    /* -------------------------------------------------- */
    /*         GET /api/real-estates/{id}                 */
    /* -------------------------------------------------- */
    #[Route('/real-estates/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $logement = $this->realEstateService->trouverParId($id);

        if (!$logement) {
            return $this->json([
                'success' => false,
                'message' => "Logement #$id introuvable",
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data'    => $this->realEstateService->serialiser($logement),
        ], Response::HTTP_OK);
    }
}