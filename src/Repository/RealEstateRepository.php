<?php

namespace App\Repository;

use App\Entity\RealEstate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealEstate>
 */
class RealEstateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealEstate::class);
    }

    /* ---- Tous les logements en ligne ---- */
    public function findAllOnline(): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.images',    'i')
            ->leftJoin('r.categorie', 'c')
            ->addSelect('i', 'c')
            ->where('r.is_online = :online')
            ->setParameter('online', true)
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* ---- Logements limités pour la page d'accueil ---- */
    public function findForHome(int $limit = 6): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.images',    'i')
            ->leftJoin('r.categorie', 'c')
            ->addSelect('i', 'c')
            ->where('r.is_online = :online')
            ->setParameter('online', true)
            ->orderBy('r.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /* ---- Un logement par slug ---- */
    public function findOneBySlug(string $slug): ?RealEstate
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.images',    'i')
            ->leftJoin('r.categorie', 'c')
            ->addSelect('i', 'c')
            ->where('r.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /* ---- Logements par catégorie ---- */
    public function findByCategorie(string $categorieSlug): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.images',    'i')
            ->leftJoin('r.categorie', 'c')
            ->addSelect('i', 'c')
            ->where('r.is_online = :online')
            ->andWhere('c.slug = :slug')
            ->setParameter('online', true)
            ->setParameter('slug',   $categorieSlug)
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}