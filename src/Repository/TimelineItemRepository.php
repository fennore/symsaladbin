<?php

namespace App\Repository;

use App\Entity\Item\TimelineItem;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method TimelineItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineItem[]    findAll()
 * @method TimelineItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineItemRepository extends AbstractBatchableEntityRepository
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, TimelineItem::class);
    }

    public function getTimelineItems(): IterableResult
    {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->iterate();
    }

    public function getTimelineItemFromPath(string $pathString): ?TimelineItem
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.path = :path')
            ->setParameter('path', $pathString)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Writes a new TimelineItem Entity to database.
     *
     * @param bool $useBatch
     */
    public function createTimelineItem(TimelineItem $item, $useBatch = true)
    {
        if (!is_null($item->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($item);
        }
        $this->persistTimelineItem($item, $useBatch);
    }

    /**
     * Updates TimelineItem Entity in database.
     *
     * @param bool $useBatch
     */
    public function updateTimelineItem(TimelineItem $item, $useBatch = true)
    {
        if (is_null($item->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($item, 'update');
        }
        $this->persistTimelineItem($item, $useBatch);
    }

    /**
     * Removes TimelineItem Entity from database.
     *
     * @param bool $useBatch
     */
    public function deleteTimelineItem(TimelineItem $item, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($item);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the TimelineItem Entity data in the database.
     *
     * @param bool $useBatch
     */
    protected function persistTimelineItem(TimelineItem $item, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($item);
        $this->startTransaction($useBatch);
    }
}
