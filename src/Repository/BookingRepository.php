<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Enum\BookingStatus;
use App\Entity\User;
use App\Entity\RealEstate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /* ---- Toutes les réservations pour un hôte ---- */
    public function findByHost(User $host): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.realEstate', 'r')
            ->leftJoin('b.guest',      'g')
            ->leftJoin('r.images',     'i')
            ->addSelect('r', 'g', 'i')
            ->where('r.owner = :host')
            ->setParameter('host', $host)
            ->orderBy('b.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* ---- Réservations à venir pour un hôte ---- */
    public function findUpcomingByHost(User $host, int $limit = 5): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.realEstate', 'r')
            ->leftJoin('b.guest',      'g')
            ->leftJoin('r.images',     'i')
            ->addSelect('r', 'g', 'i')
            ->where('r.owner = :host')
            ->andWhere('b.date_arrivee >= :now')
            ->andWhere('b.statut IN (:statuts)')
            ->setParameter('host',    $host)
            ->setParameter('now',     new \DateTimeImmutable())
            ->setParameter('statuts', [BookingStatus::CONFIRME, BookingStatus::EN_ATTENTE, BookingStatus::A_CONFIRMER])
            ->orderBy('b.date_arrivee', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /* ---- Revenus du mois pour un hôte ---- */
    public function getRevenueMoisByHost(User $host): float
    {
        $debut = new \DateTimeImmutable('first day of this month 00:00:00');
        $fin   = new \DateTimeImmutable('last day of this month 23:59:59');

        $result = $this->createQueryBuilder('b')
            ->select('SUM(b.montant)')
            ->leftJoin('b.realEstate', 'r')
            ->where('r.owner = :host')
            ->andWhere('b.statut = :statut')
            ->andWhere('b.date_arrivee BETWEEN :debut AND :fin')
            ->setParameter('host',   $host)
            ->setParameter('statut', BookingStatus::CONFIRME)
            ->setParameter('debut',  $debut)
            ->setParameter('fin',    $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /* ---- Comptage des réservations à venir pour un hôte ---- */
    public function countUpcomingByHost(User $host): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->leftJoin('b.realEstate', 'r')
            ->where('r.owner = :host')
            ->andWhere('b.date_arrivee >= :now')
            ->andWhere('b.statut IN (:statuts)')
            ->setParameter('host',    $host)
            ->setParameter('now',     new \DateTimeImmutable())
            ->setParameter('statuts', [BookingStatus::CONFIRME, BookingStatus::EN_ATTENTE])
            ->getQuery()
            ->getSingleScalarResult();
    }

	public function findByRealEstate(RealEstate $re): array
	{
		return $this->createQueryBuilder('b')
			->where('b.realEstate = :re')
			->andWhere('b.statut IN (:statuts)')
			->setParameter('re', $re)
			->setParameter('statuts', [
				BookingStatus::CONFIRME,
				BookingStatus::EN_ATTENTE,
				BookingStatus::A_CONFIRMER,
			])
			->orderBy('b.date_arrivee', 'ASC')
			->getQuery()
			->getResult();
	}
}