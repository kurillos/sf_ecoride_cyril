<?php

namespace App\Repository;

use App\Document\Review;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

/**
 * @extends ServiceDocumentRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function save(Review $entity, bool $flush = false): void
    {
        $this->getDocumentManager()->persist($entity);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function remove(Review $entity, bool $flush = false): void
    {
        $this->getDocumentManager()->remove($entity);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }
}
