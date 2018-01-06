<?php

namespace App\Repository;

use App\Entity\Log;
use App\Handler\DbBatchHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\ORMInvalidArgumentException;

class LogRepository extends AbstractBatchableEntityRepository
{
    public function __construct(RegistryInterface $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Log::class);
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
     * Writes a new Log Entity to database.
     *
     * @param Log $log
     */
    public function createLog(Log $log, $useBatch = true)
    {
        if (!is_null($log->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($log);
        }
        $this->persistLog($log, $useBatch);
    }

    /**
     * Updates Log Entity in database.
     *
     * @param Log $log
     */
    public function updateLog(Log $log, $useBatch = true)
    {
        if (is_null($log->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($log, 'update');
        }
        $this->persistLog($log, $useBatch);
    }

    /**
     * Removes Log Entity from database.
     *
     * @param Log $log
     */
    public function deleteLog(Log $log, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($log);
        $this->startTransaction($useBatch);
    }

    /**
     * Creates or updates the Log Entity data in the database.
     *
     * @param Log $log
     */
    protected function persistLog(Log $log, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($log);
        $this->startTransaction($useBatch);
    }
}
