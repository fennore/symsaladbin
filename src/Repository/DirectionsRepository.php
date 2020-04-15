<?php

namespace App\Repository;

use App\Entity\Directions;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\Query\Expr\Join;

class DirectionsRepository extends AbstractBatchableEntityRepository
{
    use Traits\RepositoryStageTrait;

    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Directions::class);
    }

    /**
     * @return IterableResult
     */
    public function getDirections(int $stage)
    {
        $qb = $this->createQueryBuilder('d');
        $qb
            ->join('d.origin', 'l', Join::ON, 'd.stage = :stage')
            ->setParameter(':stage', $stage)
            ->orderBy('l.weight', 'ASC');

        return $qb->getQuery()->iterate();
    }

    /**
     * Writes a new Directions Entity to database.
     */
    public function createDirections(Directions $directions, $useBatch = true)
    {
        if (!is_null($directions->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($directions);
        }
        $this->persistDirections($directions, $useBatch);
    }

    /**
     * Updates Directions Entity in database.
     */
    public function updateDirections(Directions $directions, $useBatch = true)
    {
        if (is_null($directions->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($directions, 'update');
        }
        $this->persistDirections($directions, $useBatch);
    }

    /**
     * Removes Directions Entity from database.
     */
    public function deleteDirections(Directions $directions, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($directions);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the Directions Entity data in the database.
     */
    protected function persistDirections(Directions $directions, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($directions);
        $this->startTransaction($useBatch);
    }
}
