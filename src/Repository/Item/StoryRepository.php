<?php

namespace App\Repository\Item;

use App\Entity\Item\Story;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\UnitOfWork;

/**
 * @method Story|null find($id, $lockMode = null, $lockVersion = null)
 * @method Story|null findOneBy(array $criteria, array $orderBy = null)
 * @method Story[]    findAll()
 * @method Story[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class StoryRepository implements StoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Story::class, 's');
    }

    /**
     * Writes a new Story Entity to database.
     */
    public function createStory(Story $story, bool $useBatch = true): void
    {
        if (!is_null($story->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($story);
        }
        $this->persistStory($story, $useBatch);
    }

    /**
     * Updates Story Entity in database.
     */
    public function updateStory(Story $story, bool $useBatch = true): void
    {
        if (is_null($story->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($story, 'updated');
        }

        if ($this->entityHasState(UnitOfWork::STATE_DETACHED)) {
            throw ORMInvalidArgumentException::detachedEntityCannot($story, 'updated');
        }

        $this->persistStory($story, $useBatch);
    }

    public function updateStories(Story ...$stories): void
    {
        $mapper = [];
        $ids = [];
        foreach ($stories as $story) {
            $id = $story->getId();
            $mapper[$id] = $story;
            $ids[] = $id;
        }

        foreach (
            $this
                ->createQueryBuilder('s')
                ->addCriteria($this->getIdListCriteria($ids))
                ->getQuery()
                ->iterate() as $row
        ) {
            $story = $row[0];
            $id = $story->getId();
            $this->prepareItemForUpdate($story, $mapper[$id] ?? null);
//            $story->setLink($items);
            $this->updateStory($story);
        }
    }

    public function deleteStory(Story $story, bool $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($story);
        $this->startTransaction($useBatch);
    }

    protected function persistStory(Story $story, bool $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($story);
        $this->startTransaction($useBatch);
    }
}
