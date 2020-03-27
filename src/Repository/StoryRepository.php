<?php

namespace App\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\ORMInvalidArgumentException;
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
    public function createStory(Story $story, $useBatch = true)
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
    public function updateStory(Story $story, $useBatch = true)
    {
        if (is_null($story->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($story, 'update');
        }
        $this->persistStory($story, $useBatch);
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
}
