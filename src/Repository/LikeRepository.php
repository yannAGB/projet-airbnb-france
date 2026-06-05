<?php

namespace App\Repository;

use App\Entity\Like;
use App\Entity\RealEstate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    /* ---- Avis d'un logement ---- */
    public function findByRealEstate(RealEstate $re): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.reviewer', 'u')
            ->addSelect('u')
            ->where('l.realEstate = :re')
            ->setParameter('re', $re)
            ->orderBy('l.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* ---- Note moyenne d'un logement ---- */
    public function getNoteMoyenne(RealEstate $re): float
    {
        $result = $this->createQueryBuilder('l')
            ->select('AVG(l.review)')
            ->where('l.realEstate = :re')
            ->setParameter('re', $re)
            ->getQuery()
            ->getSingleScalarResult();

        return round((float) ($result ?? 0), 1);
    }
}