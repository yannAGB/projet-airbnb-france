<?php

namespace App\Controller\Api;

use App\Service\CategorieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_categorie_')]
final class ApiCategorieController extends AbstractController
{
    public function __construct(
        private CategorieService $categorieService,
    ) {}

    /* -------------------------------------------------- */
    /*         GET /api/categories                        */
    /* -------------------------------------------------- */
    #[Route('/categories', name: 'list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $parentesOnly = $request->query->getBoolean('parentes', false);

        $categories = $parentesOnly
            ? $this->categorieService->getCategoriesParentes()
            : $this->categorieService->getToutesLesCategories();

        return $this->json([
            'success' => true,
            'total'   => count($categories),
            'data'    => $this->categorieService->serialiserListe($categories),
        ], Response::HTTP_OK);
    }
}