<?php

namespace App\Repository\Log;

use App\Entity\Log;
use App\Handler\DbBatchHandler;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMInvalidArgumentException;

class LogRepository extends AbstractBatchableEntityRepository
{
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler)
    {
        parent::__construct($registry, $batchHandler, Log::class);
    }

    /**
     * Writes a new Log Entity to database.
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
     */
    public function deleteLog(Log $log, $useBatch = true)
    {
        $em = $this->getEntityManager();
        $em->remove($log);
        $this->startTransaction($useBatch);
    }

    public function emptyLog()
    {
        // Flush any queries waiting
        $this
            ->getEntityManager()
            ->flush();
        // Execute bulk query
        $qb = $this
            ->createQueryBuilder('l');
        $qb
            ->delete()
            ->where($qb->expr()->gte('l.id', ':id'))
            ->setParameter(':id', 0)
            ->getQuery()
            ->execute();
    }

    /**
     * Creates or updates the Log Entity data in the database.
     */
    protected function persistLog(Log $log, $useBatch)
    {
        $em = $this->getEntityManager();
        $em->persist($log);
        $this->startTransaction($useBatch);
    }
}
