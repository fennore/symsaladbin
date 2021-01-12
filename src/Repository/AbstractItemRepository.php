<?php

namespace App\Repository;

use App\Entity\Item\Item;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Internal\Hydration\IterableResult;

abstract class AbstractItemRepository extends AbstractBatchableEntityRepository
{
    protected string $alias;

    /** @param string $alias Alias for the repository entity table */
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler, string $entityClass, string $alias)
    {
        parent::__construct($registry, $batchHandler, $entityClass);
        $this->alias = $alias;
    }

    public function getAll(): Traversable
    {
        foreach (
            $this->createQueryBuilder($this->alias)
                ->getQuery()
                ->iterate() 
            as $row
        ) {
            yield $row[0];
        }
    }

    public function getRange(int $offset, int $limit, bool $showDisabled = false): Traversable
    {
        $qb = $this->createQueryBuilder($this->alias);
        if (!$showDisabled) {
            $qb
                ->andWhere("{$this->alias}.status > :status")
                ->setParameter('status', 0);
        }

        foreach (
            $qb
                ->addOrderBy("{$this->alias}.weight")
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->iterate() 
            as $row
        ) {
            yield $row[0];
        }
    }

    public function getFromPath(string $pathString): ?Item
    {
        return $this->createQueryBuilder($this->alias)
            ->andWhere("{$this->alias}.path = :path")
            ->setParameter('path', $pathString)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countAll(bool $showDisabled = false): int
    {
        if (!$showDisabled) {
            return $this->countByCriteria(
                $this->getEnabledCriteria()
            );
        }

        return $this->getTotal();
    }

    public function deleteById(int ...$ids): void
    {
        $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->delete($this->getEntityName())
            ->where('id IN(:ids)')
            ->setParameter('ids', $ids);
    }

    protected function getEnabledCriteria(): Criteria
    {
        $criteria = (new Criteria());

        return $criteria->andWhere(
                $criteria->expr()->gt('status', 0));
    }

    protected function getIdListCriteria(int ...$ids): Criteria
    {
        $criteria = (new Criteria());

        return $criteria->andWhere(
                $criteria->expr()->in('id', $ids));
    }

    protected function prepareItemForUpdate(Item $persistedItem, ?Item $newItem): void
    {
        if (null === $newItem) {
            return;
        }
        $persistedItem
            ->setWeight($newItem->getWeight())
            ->setTitle($newItem->getTitle())
            ->setContent($newItem->getContent());
        if ($persistedItem->isActive() && !$newItem->isActive()) {
            $persistedItem->setInactive();
        }
        if (!$persistedItem->isActive() && $newItem->isActive()) {
            $persistedItem->setActive();
        }
    }
}
