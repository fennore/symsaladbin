<?php

namespace App\Repository;

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
class StoryRepository extends AbstractItemRepository
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
        $entityState = $this->getEntityManager()->getUnitOfWork()->getEntityState(
            $story,
            UnitOfWork::STATE_DETACHED
        );
        if (UnitOfWork::STATE_DETACHED === $entityState) {
            throw ORMInvalidArgumentException::detachedEntityCannot($story, 'updated');
        }
        $this->persistStory($story, $useBatch);
    }

    /**
     * @param Story[] $stories
     */
    public function updateStories(array $stories): void
    {
        $matches = [];
        $ids = [];
        foreach ($stories as $story) {
            $id = $story->getId();
            $matches[$id] = $story;
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
            if (!isset($matches[$id])) {
                continue;
            }
            $story
                ->setWeight($matches[$id]->getWeight())
                ->setTitle($matches[$id]->getTitle())
                ->setContent($matches[$id]->getContent());
            if ($story->isActive() && !$matches[$id]->isActive()) {
                $story->setInactive();
            }
            if (!$story->isActive() && $matches[$id]->isActive()) {
                $story->setActive();
            }
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
