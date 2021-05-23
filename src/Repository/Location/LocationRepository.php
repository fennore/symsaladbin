<?php

namespace App\Repository\Location;

use App\Entity\Location;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\Query;

final class LocationRepository
{
    use Traits\RepositoryStageTrait;

    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Location::class);
    }

    /**
     * Get the last recorded stage.
     */
    public function getLastStage(): int
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('MAX(l.stage) AS lastStage');

        return (int) $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * Get the list of all stages.
     */
    public function getStageList(): array
    {
        $lastStage = $this->getLastStage();

        return $lastStage > 0 ? range(1, $lastStage) : [];
    }

    /**
     * Get locations by stage and weight.
     *
     * @param type $limit
     */
    public function getStageLocations(int $stage, int $weight = 0, $limit = 0): IterableResult
    {
        $qb = $this->createQueryBuilder('l');
        $expr = $qb->expr()->andX(
            $qb->expr()->gte('l.weight', ':weight'), $qb->expr()->eq('l.stage', ':stage')
        );
        $qb->where($expr)
            ->setParameter(':stage', $stage)
            ->setParameter(':weight', $weight)
            ->orderBy('l.weight', 'ASC');
        // Optionally set limit
        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->iterate();
    }

    /**
     * Writes a new Location Entity to database.
     */
    public function createLocation(Location $location, $useBatch = true)
    {
        if (!is_null($location->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($location);
        }
        $this->persistLocation($location, $useBatch);
    }

    /**
     * Updates Location Entity in database.
     */
    public function updateLocation(Location $location, $useBatch = true)
    {
        if (is_null($location->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($location, 'update');
        }
        $this->persistLocation($location, $useBatch);
    }

    /**
     * Removes Location Entity from database.
     */
    public function deleteLocation(Location $location, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($location);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the Location Entity data in the database.
     */
    protected function persistLocation(Location $location, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($location);
        $this->startTransaction($useBatch);
    }
}
