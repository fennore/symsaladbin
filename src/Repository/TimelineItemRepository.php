<?php

namespace App\Repository;

use App\Entity\TimelineItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TimelineItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineItem[]    findAll()
 * @method TimelineItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineItemRepository extends AbstractBatchableEntityRepository 
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TimelineItem::class);
    }

//    /**
//     * @return TimelineItem[] Returns an array of TimelineItem objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimelineItem
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
