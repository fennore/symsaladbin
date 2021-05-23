<?php

namespace App\Repository\Item;

use App\Entity\Item\TimelineItem;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method TimelineItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineItem[]    findAll()
 * @method TimelineItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class TimelineItemRepository extends AbstractItemRepository
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, TimelineItem::class, 't');
    }

    /**
     * Writes a new TimelineItem Entity to database.
     */
    public function createTimelineItem(TimelineItem $item, bool $useBatch = true)
    {
        if (!is_null($item->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($item);
        }
        $this->persistTimelineItem($item, $useBatch);
    }

    /**
     * Updates TimelineItem Entity in database.
     */
    public function updateTimelineItem(TimelineItem $item, bool $useBatch = true)
    {
        if (is_null($item->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($item, 'updated');
        }

        if ($this->entityHasState(UnitOfWork::STATE_DETACHED)) {
            throw ORMInvalidArgumentException::detachedEntityCannot($item, 'updated');
        }

        $this->persistTimelineItem($item, $useBatch);
    }

    public function updateTimelineItems(TimelineItem ...$items): void
    {
        $mapper = [];
        $ids = [];
        foreach ($items as $item) {
            $id = $item->getId();
            $mapper[$id] = $item;
            $ids[] = $id;
        }

        foreach (
            $this
                ->createQueryBuilder('t')
                ->addCriteria($this->getIdListCriteria(...$ids))
                ->getQuery()
                ->iterate() as $row
        ) {
            $item = $row[0];
            $id = $item->getId();
            $this->prepareItemForUpdate($item, $mapper[$id] ?? null);
//            $item->setLink($items);
            $this->updateTimelineItem($item);
        }
    }

    /**
     * Removes TimelineItem Entity from database.
     */
    public function deleteTimelineItem(TimelineItem $item, bool $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($item);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the TimelineItem Entity data in the database.
     */
    protected function persistTimelineItem(TimelineItem $item, bool $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($item);
        $this->startTransaction($useBatch);
    }
}