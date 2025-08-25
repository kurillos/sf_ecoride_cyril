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

    public function getEarningsByDate(): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.trip', 't')
            ->select('SUBSTRING(t.departureTime, 1, 10) as date', 'SUM(b.seats * t.pricePerSeat) as earnings')
            ->andWhere('b.status = :status')
            ->setParameter('status', 'confirmed')
            ->groupBy('date')
            ->orderBy('date', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getTotalEarnings(): float
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.trip', 't')
            ->select('SUM(b.seats * t.pricePerSeat) as total')
            ->andWhere('b.status = :status')
            ->setParameter('status', 'confirmed');

        $result = $qb->getQuery()->getSingleScalarResult();

        return (float) $result;
    }
}
