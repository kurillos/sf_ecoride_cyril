<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    //    /**
    //     * @return Trip[] Returns an array of Trip objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findBySearchCriteria(?string $departure, ?string $destination, ?string $date): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($departure) {
            $qb->andWhere('t.departureLocation LIKE :departure')
                ->setParameter('departure', '%' . $departure . '%');
        }

        if ($destination) {
            $qb->andWhere('t.destinationLocation LIKE :destination')
                ->setParameter('destination', '%' . $destination . '%');
        }

        if ($date) {
            $dateObject = new \DateTime($date);
            $qb->andWhere('t.departureTime >= :date')
                ->setParameter('date', $dateObject->format('Y-m-d 00:00:00'));
        }

        return $qb->getQuery()->getResult();
    }
}
