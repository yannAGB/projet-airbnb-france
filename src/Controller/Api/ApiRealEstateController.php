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
        /* Paramètre optionnel ?limit=6 pour la home */
        $limit     = $request->query->getInt('limit', 0);
        $categorie = $request->query->get('categorie');

        if ($categorie) {
            $logements = $this->realEstateService->trouverParCategorie($categorie);
        } elseif ($limit > 0) {
            $logements = $this->realEstateService->getLogementsAccueil($limit);
        } else {
            $logements = $this->realEstateService->getTousLesLogements();
        }

        if (empty($logements)) {
            return $this->json([
                'success' => false,
                'message' => 'Aucun logement trouvé',
                'data'    => [],
            ], Response::HTTP_OK);
        }

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

    /* -------------------------------------------------- */
    /*         GET /api/real-estates/slug/{slug}          */
    /* -------------------------------------------------- */
    #[Route('/real-estates/slug/{slug}', name: 'show_by_slug', methods: ['GET'])]
    public function showBySlug(string $slug): JsonResponse
    {
        $logement = $this->realEstateService->trouverParSlug($slug);

        if (!$logement) {
            return $this->json([
                'success' => false,
                'message' => "Logement « $slug » introuvable",
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data'    => $this->realEstateService->serialiser($logement),
        ], Response::HTTP_OK);
    }
}