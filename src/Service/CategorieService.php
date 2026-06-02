<?php

namespace App\Service;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;

class CategorieService
{
    public function __construct(
        private CategorieRepository $categorieRepository,
    ) {}

    public function getToutesLesCategories(): array
    {
        return $this->categorieRepository->findToutes();
    }

    public function getCategoriesParentes(): array
    {
        return $this->categorieRepository->findParentes();
    }

    /* ---- Sérialisation ---- */
    public function serialiser(Categorie $categorie): array
    {
        return [
            'id'          => $categorie->getId(),
            'title'       => $categorie->getTitle(),
            'slug'        => $categorie->getSlug(),
            'description' => $categorie->getDescription(),
            'parent'      => $categorie->getParent() ? [
                'id'    => $categorie->getParent()->getId(),
                'title' => $categorie->getParent()->getTitle(),
                'slug'  => $categorie->getParent()->getSlug(),
            ] : null,
            'nbLogements' => $categorie->getRealEstate()->count(),
        ];
    }

    public function serialiserListe(array $categories): array
    {
        return array_map(
            fn($c) => $this->serialiser($c),
            $categories
        );
    }
}