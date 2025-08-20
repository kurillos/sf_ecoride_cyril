<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function findFutureBookingsByUser($user): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.trip', 't')
            ->andWhere('b.user = :user')
            ->andWhere('t.departureTime > :now')
            ->andWhere('b.status != :status')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->setParameter('status', 'cancelled')
            ->getQuery()
            ->getResult();
    }
}
