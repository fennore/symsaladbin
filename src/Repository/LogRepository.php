<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\ORMInvalidArgumentException;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
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
     * Writes a new Log Entity to database
     * @param Log $log
     */
    public function createLog(Log $log) {
        if(!is_null($log->getId())) {
            throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($log);
        }
        $this->persistLog($log);
    }

    /**
     * Updates Log Entity in database
     * @param Log $log
     */
    public function updateLog(Log $log) {
        if(is_null($log->getId())) {
            throw ORMInvalidArgumentException::entityHasNoIdentity($log, 'update');
        }
        $this->persistLog($log);
    }

    /**
     * Removes Log Entity from database
     * @param Log $log
     */
    public function deleteLog(Log $log) {
        $this->getEntityManager()->remove($log);
    }

    /**
     * Creates or updates the Log Entity data in the database.
     * @param Log $log
     */
    protected function persistLog(Log $log) {
        $this->getEntityManager()->persist($log);
        //$this->getEntityManager()->flush();
    }
}
