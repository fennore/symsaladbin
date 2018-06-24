<?php

namespace App\Repository;

use App\Entity\Item\Story;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Handler\DbBatchHandler;

/**
 * @method Story|null find($id, $lockMode = null, $lockVersion = null)
 * @method Story|null findOneBy(array $criteria, array $orderBy = null)
 * @method Story[]    findAll()
 * @method Story[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryRepository extends AbstractBatchableEntityRepository
{
    public function __construct(RegistryInterface $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Story::class);
    }

//    /**
//     * @return Story[] Returns an array of Story objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
}
