<?php

namespace App\Repository;

use App\Handler\DbBatchHandler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractBatchableEntityRepository extends ServiceEntityRepository
{
    use Traits\RepositoryGeneralTrait;

    protected DbBatchHandler $batchHandler;

    /**
     * @param string $entityClass Entity class for the repository
     */
    public function __construct(ManagerRegistry $registry, DbBatchHandler $batchHandler, string $entityClass)
    {
        $this->batchHandler = $batchHandler;

        parent::__construct($registry, $entityClass);
    }

    protected function startTransaction($useBatch)
    {
        $em = $this->getEntityManager();
        if ($useBatch) {
            $this->batchHandler->addToBatch($em, $this->getEntityName());
        } else {
            $em->flush();
        }
    }
}
