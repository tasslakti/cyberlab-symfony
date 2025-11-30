<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Trouve les reservations d'un utilisateur
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les reservations (pour admin/instructeur)
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les reservations par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', $status)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les reservations en attente de confirmation
     */
    public function findPendingReservations(): array
    {
        return $this->findByStatus(Reservation::STATUS_PENDING);
    }

    /**
     * Statistiques pour le dashboard
     */
    public function getReservationStats(): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as total')
            ->addSelect('SUM(CASE WHEN r.status = :pending THEN 1 ELSE 0 END) as pending')
            ->addSelect('SUM(CASE WHEN r.status = :confirmed THEN 1 ELSE 0 END) as confirmed')
            ->addSelect('SUM(CASE WHEN r.status = :cancelled THEN 1 ELSE 0 END) as cancelled')
            ->setParameter('pending', Reservation::STATUS_PENDING)
            ->setParameter('confirmed', Reservation::STATUS_CONFIRMED)
            ->setParameter('cancelled', Reservation::STATUS_CANCELLED)
            ->getQuery()
            ->getSingleResult();

        return [
            'total' => (int) $result['total'],
            'pending' => (int) $result['pending'],
            'confirmed' => (int) $result['confirmed'],
            'cancelled' => (int) $result['cancelled']
        ];
    }
}