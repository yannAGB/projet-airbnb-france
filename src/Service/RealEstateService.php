<?php

namespace App\Service;

use App\Entity\RealEstate;
use App\Repository\RealEstateRepository;

class RealEstateService
{
    public function __construct(
        private RealEstateRepository $realEstateRepository,
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

    /* -------------------------------------------------- */
    /*               Sérialisation JSON                   */
    /* -------------------------------------------------- */

    public function serialiser(RealEstate $logement): array
    {
        /* Première image disponible */
        $premiereImage = null;
        if (!$logement->getImages()->isEmpty()) {
            $premiereImage = $logement->getImages()->first()->getName();
        }

        /* Toutes les images */
        $images = $logement->getImages()->map(
            fn($img) => $img->getName()
        )->toArray();

        return [
            'id'          => $logement->getId(),
            'title'       => $logement->getTitle(),
            'description' => $logement->getDescription(),
            'slug'        => $logement->getSlug(),
            'price'       => $logement->getPrice(),
            'promotion'   => $logement->getPromotion(),
            'image'       => $premiereImage,
            'images'      => array_values($images),
            'type'        => $logement->getCategorie()?->getTitle(),
            'categorie'   => [
                'id'    => $logement->getCategorie()?->getId(),
                'title' => $logement->getCategorie()?->getTitle(),
                'slug'  => $logement->getCategorie()?->getSlug(),
            ],
            'capacite'    => [
                'maxTravelers' => $logement->getMaxTravelers(),
                'adults'       => $logement->getAdults(),
                'children'     => $logement->getChildren(),
                'babies'       => $logement->getBabies(),
            ],
            'adresse'     => [
                'numero'      => $logement->getStreetNumber(),
                'rue'         => $logement->getStreetName(),
                'codePostal'  => $logement->getPostalCode(),
                'complement'  => $logement->getAddressLine2(),
                'ville'       => $logement->getCity(),
                'pays'        => $logement->getCountry(),
            ],
            'coordonnees' => [
                'latitude'  => $logement->getLatitude(),
                'longitude' => $logement->getLongitude(),
            ],
            'likes'       => $logement->getLikes(),
            'is_online'   => $logement->isOnline(),
            'created_at'  => $logement->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at'  => $logement->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public function serialiserListe(array $logements): array
    {
        return array_map(
            fn($logement) => $this->serialiser($logement),
            $logements
        );
    }
}