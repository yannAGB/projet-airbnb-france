<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    /* ---- Toutes les catégories parentes ---- */
    public function findParentes(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.categories', 'sub')
            ->addSelect('sub')
            ->where('c.parent IS NULL')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* ---- Toutes les catégories ---- */
    public function findToutes(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.parent', 'p')
            ->addSelect('p')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* ---- Comptage total ---- */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}