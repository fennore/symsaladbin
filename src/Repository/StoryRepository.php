<?php

namespace App\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\{UnitOfWork, ORMInvalidArgumentException};
use Doctrine\Common\Collections\Criteria;
use App\Entity\Item\Story;
use App\Handler\DbBatchHandler;

/**
 * @method Story|null find($id, $lockMode = null, $lockVersion = null)
 * @method Story|null findOneBy(array $criteria, array $orderBy = null)
 * @method Story[]    findAll()
 * @method Story[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryRepository extends AbstractBatchableEntityRepository
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Story::class);
    }

    public function getAllStories(): IterableResult
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->iterate();
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param bool $showDisabled
     * @return IterableResult
     */
    public function getStories(int $offset, int $limit, bool $showDisabled = false): IterableResult
    {
        $qb = $this->createQueryBuilder('s');
        if(!$showDisabled) {
            $qb
                ->andWhere('s.status > :status')
                ->setParameter('status', 0);
        }
        return $qb
            ->addOrderBy('s.weight')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->iterate();
    }

    /**
     * @param bool $showDisabled
     * @return int
     */
    public function countStories(bool $showDisabled = false): int
    {
        if(!$showDisabled) {
            return $this->countByCriteria(
                $this->getEnabledCriteria()
            );
        }

        return $this->getTotal();
    }

    public function getStoryFromPath(string $pathString): ?Story
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.path = :path')
            ->setParameter('path', $pathString)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Writes a new Story Entity to database.
     *
     * @param Story $story
     * @param bool  $useBatch
     */
    public function createStory(Story $story, $useBatch = true): void
    {
        if (!is_null($story->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($story);
        }
        $this->persistStory($story, $useBatch);
    }

    /**
     * Updates Story Entity in database.
     *
     * @param Story $story
     * @param bool  $useBatch
     */
    public function updateStory(Story $story, $useBatch = true): void
    {
        if (is_null($story->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($story, 'updated');
        }
        $entityState = $this->getEntityManager()->getUnitOfWork()->getEntityState(
            $story, 
            UnitOfWork::STATE_DETACHED
        );
        if($entityState === UnitOfWork::STATE_DETACHED) {
            throw ORMInvalidArgumentException::detachedEntityCannot($story, 'updated');
        }
        $this->persistStory($story, $useBatch);
    }

    /**
     * @param Story[] $stories
     * @return void
     */
    public function updateStories(array $stories): void
    {
        $matches = [];
        $ids = [];
        foreach($stories as $story) {
            $id = $story->getId();
            $matches[$id] = $story;
            $ids[] = $id;
        }

        foreach(
            $this
                ->createQueryBuilder('s')
                ->addCriteria($this->getIdListCriteria($ids))
                ->getQuery()
                ->iterate() as $row
        ) {
            $story = $row[0];
            $id = $story->getId();
            if(!isset($matches[$id])) {
                continue;
            }
            $story
                ->setWeight($matches[$id]->getWeight())
                ->setTitle($matches[$id]->getTitle())
                ->setContent($matches[$id]->getContent());
            if($story->isActive() && !$matches[$id]->isActive()) {
                $story->setInactive();
            }
            if(!$story->isActive() && $matches[$id]->isActive()) {
                $story->setActive();
            }
//            $story->setLink($items);
            $this->updateStory($story);
        }
    }

    /**
     * Removes Story Entity from database.
     *
     * @param Story $story
     * @param bool  $useBatch
     */
    public function deleteStory(Story $story, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($story);
        $this->startTransaction($useBatch);
    }

    /**
     * @param array $ids
     */
    public function deleteStoriesById(array $ids) {
        $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->delete($this->getEntityName())
            ->where('id IN(:ids)')
            ->setParameter('ids', $ids);
    }

    /**
     * Creates or updates the Story Entity data in the database.
     *
     * @param Story $story
     * @param bool  $useBatch
     */
    protected function persistStory(Story $story, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($story);
        $this->startTransaction($useBatch);
    }

    protected function getEnabledCriteria() {
        $criteria = (new Criteria);
        
        return $criteria->andWhere(
                $criteria->expr()->gt('status', 0));
    }

    protected function getIdListCriteria($ids) {
        $criteria = (new Criteria);
        
        return $criteria->andWhere(
                $criteria->expr()->in('id', $ids));
    }
}
