<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /* ---- Dernière notification reçue ---- */
    public function findLatestForUser(User $user): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.sender', 's')
            ->addSelect('s')
            ->where('n.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /* ---- Nombre de notifications non lues ---- */
    public function countUnreadForUser(User $user): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.recipient = :user')
            ->andWhere('n.is_read = :unread')
            ->setParameter('user',   $user)
            ->setParameter('unread', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}