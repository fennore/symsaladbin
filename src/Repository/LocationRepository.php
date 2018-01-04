<?php

namespace App\Repository;

use App\Entity\Location;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Handler\DbBatchHandler;
use Doctrine\ORM\Query;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class LocationRepository extends AbstractBatchableEntityRepository
{
    
    public function __construct(RegistryInterface $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Location::class);
    }

    /*
      public function findBySomething($value)
      {
      return $this->createQueryBuilder('l')
      ->where('l.something = :value')->setParameter('value', $value)
      ->orderBy('l.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /**
     * Get the last recorded stage.
     * @return int
     */
    public function getLastStage(): int
    {
        $qb = $this->createQueryBuilder();
        $qb->select('MAX(l.stage) AS lastStage');
        return (int) $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * Get locations by stage and weight
     * @param int $stage
     * @param int $weight
     * @param type $limit
     * @return IterableResult
     */
    public function getStageLocations(int $stage, int $weight, $limit = 0): IterableResult
    {
        $qb = $this->createQueryBuilder();
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
     * Writes a new Location Entity to database
     * @param Location $location
     */
    public function createLocation(Location $location, $useBatch = true) {
        if(!is_null($location->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($location);
        }
        $this->persistLocation($location, $useBatch);
    }

    /**
     * Updates Location Entity in database
     * @param Location $location
     */
    public function updateLocation(Location $location, $useBatch = true) {
        if(is_null($location->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($location, 'update');
        }
        $this->persistLocation($location, $useBatch);
    }

    /**
     * Removes Location Entity from database
     * @param Location $location
     */
    public function deleteLocation(Location $location, $useBatch = true) {
        $em = $this->getEntityManager();
        $em->remove($location);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the Location Entity data in the database.
     * @param Location $location
     */
    protected function persistLocation(Location $location, $useBatch) {
        $em = $this->getEntityManager();
        $em->persist($location);
        $this->startTransaction($useBatch);
    }

}
